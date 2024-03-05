<?php 

/**
 * 
 * on veut pouvoir renvoyer un objetr de type scrapy
 * avec un ensemble de méthodes 
 */
namespace App\Service;

use Faker\Factory;
use App\Entity\Type;
use  App\Entity\Game;
use App\Entity\Theme;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\GameTmp;
use App\Entity\GameType;
use App\Entity\Illustrator;
use App\Repository\GameRepository;
use App\Repository\TypeRepository;
use App\Repository\ThemeRepository;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use App\Repository\GameTmpRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\IllustratorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;

class Scrapy {

    private $invalide;//status du jeu quand une erreur est survenue
    private $flag=0;
    private $manager;
    private $tmpFile;
    private $url;
    private $browser;
    private $gameArray;
    private $formats=array(
        'name'=>['line'=>'/class="name_product"/','itemInLine'=>'/>((\w+\s*\W*)+)</'],
        'duration'=>[],
        'shortDescription'=>['line'=>'/class="desc_short"/','itemInLine'=>'/>((\w+\s*\W*)+)</'],
        'nbPlayers'=>[],
        'blockGame'=>['line'=>'/class="container_info/'],
        'href'=>["line"=>'/^<a\shref="\/produit\/\d+/',"itemInLine"=>'/"(.*)"/']  
    );

    public function __construct(GameRepository $gameRepository,TypeRepository $typeRepo,GameTmpRepository $gameTmpRepo,
    AuthorRepository $auteurRepo,IllustratorRepository $dessinateurRepo,ThemeRepository $themeRepo,EditorRepository $editorRepo,EntityManagerInterface $manager){

        $this->tmpFile=__DIR__."/../../docs/tmpFile.txt";
        $this->browser=new HttpBrowser(HttpClient::create());
        $this->gameArray=[];
        // $this->manager=new ManagerRegistry;
        $this->gameRepository=$gameRepository;
        $this->typeRepo=$typeRepo;
        $this->gameTmpRepo=$gameTmpRepo;
        $this->auteurRepo=$auteurRepo;
        $this->dessinateurRepo=$dessinateurRepo;
        $this->themeRepo=$themeRepo;
        $this->editorRepo=$editorRepo;
        $this->manager=$manager;
        
        $this->url='http://www.play-in.com/jeux_de_societe/recherche/?p=';
        

    }

    //va renvoyer les informations de chaque ligne correspondante
    //critères preg

    public function getFlag()
    {
        return $this->flag;
    }
    public function crawler(string $row,string $needle)
    {


            if(preg_match($this->formats[$needle]['line'],$row)){

                preg_match($this->formats[$needle]['itemInLine'],$row,$matches);
                if(isset($matches[1])){

                    return $matches[1];
                }
                
            }
            else{
                
                false;
            }

        }
    
        //une fois sur la page de détail d'un jeu  on lance le crawler dédié


    public function crawlerDetail(GameTmp $gameTmp){

            $name=$gameTmp->getName();
            $game = new Game();
            $game->setTitle($name);
            $faker = Factory::create();

            $this->invalide=FALSE;
            $this->browser->request('GET','https://play-in.com/'.$gameTmp->getHref());//la ressource browser

            $contentPageString=($this->browser->getResponse())->getContent();
            $contentPageString=mb_convert_encoding($contentPageString,'utf-8');
            file_put_contents($this->tmpFile,$contentPageString);
            //on récupére le contenu du txt sous la forme d'un tableau
            $arrayContent=file($this->tmpFile);
            $arrayResponse=[
                'nom'=>$name,
                'types'=>[],
                'themes'=>[],
                'editeur'=>'',
                'auteurs'=>[],
                'dessinateurs'=>[],
                'duration'=>'',
                'nbJoueursMin'=>'',
                'nbJoueursMax'=>'',
                'age'=>'',
                'image'=>'',
                'shortDescription'=>'',
                'longDescription'=>""
            ];

            $arrayPreg=[
                'image'=>'/https:\/\/www.play.in.com\/img\/product\/l\/(\w*\W)*.[jpg]*[png]+/',
                'duration'=>'/^<th\sscope="row">Durée<\/th>/',
                'age'=>'/^<th\sscope="row">Âge<\/th>/',
                'nbJoueurs'=>'/^<th\sscope="row">Nombre\sde\sjoueurs<\/th>/',
                'types'=>'/^<td\sclass="text"><a\shref="\/jeux_de_societe\/recherche\/\?type/',
                'themes'=>'/^<th\sscope="row">Thème\(s\)<\/th>$/',
                'auteurs'=>'/^<th\sscope="row">Auteur\(s\)<\/th>$/',
                'editeur'=>'/^<th\sscope="row">Éditeur\(s\)<\/th>$/',
                'dessinateurs'=>'/<th\sscope="row">Illustrateur\(s\)<\/th>/',
                'shortDescription'=>'/<div\sclass="shortdesc_product">(.*)<\/div/',
                'longDescription'=>'/<\/div><div\sclass="tab\sbloc_tab\stab_desc\stab_selected">/'

            ];
            $decodeString=['&eacute;','&egrave;','&agrave;','&nbsp;','&ecirc;','&ccedil;','&ugrave;','&acirc;','&ouml;','&amp;','&ucirc;','&hellip;','&ocirc;','&iuml;','&icirc;'];
            $replaceString=['é','è','à',' ','ê','ç','ù','â','ö','&','û','...','ô','ï','î'];

            $a=0;

            for($a=0;$a<count($arrayContent);$a++)
            {

                $line=$arrayContent[$a];
                // longue description

                if(preg_match($arrayPreg['longDescription'],$line)){

                    $longDescriptionArray=[];
                    while(!preg_match('/^<th\sscope="row">Langue<\/th>$/',$arrayContent[$a])){

                        
                        $str=mb_convert_encoding(strip_tags($arrayContent[$a]),'utf-8');
                        $str=str_replace($decodeString,$replaceString,$str);
                        $longDescriptionArray[]=$str;

                        $a++;
                    }
                    if(!empty(implode(PHP_EOL,$longDescriptionArray))){
                    
                        $arrayResponse['longDescription']=implode(PHP_EOL,$longDescriptionArray);

                    }
                    else{

                        $this->writeLog($name,"longDescription");
                        break;
                    }
                }
                //image
                if(preg_match($arrayPreg['image'],$line,$matches)){
                    
                    //vérification 
                    if(!empty($matches)){
                        $arrayResponse['image']=$matches[0];
                        
                    }
                    else{

                        $this->writeLog($name,"image");
                        break;
                        //on inscrit l'erreur dans les log et on break
                      
                    }
                    
                }

                //si line=short descri
                if(preg_match($arrayPreg['shortDescription'],$line,$matches))
                {
                    if(!empty($matches)){
                    
                        $arrayResponse['shortDescription']=$matches[1];

                    }
                    else{

                        $this->writeLog($name,"shortDescription");
                        break;
                    }
                }
                //si la line = durée
                if(preg_match($arrayPreg["duration"],$line)){

                    preg_match('/<div>((\d*\w*[éè]*\s*)*)/',$arrayContent[$a+1],$matches);

                    if(!empty($matches)){
                    
                        $arrayResponse['duration']=$matches[1];

                    }
                    else{

                        $this->writeLog($name,"duration");
                        break;
                    }

                }      
                //si la line = age
                
                if(preg_match($arrayPreg["age"],$line)){

                    preg_match('/<div>(\w*[À]*\s)*(\d*)/',$arrayContent[$a+2],$matches);
                  
                    if(!empty($matches)){

                        $arrayResponse['age']=$matches[1];
                    }

                    else{

                        $this->writeLog($name,"age");

                    }
                }                
                //si la line = nbJoueurs
                if(preg_match($arrayPreg['nbJoueurs'],$line)){

                    
                    preg_match('/<div>\w*\s*(\d*)\s*à\s*(\d*)/',$arrayContent[$a+2],$matches);
                    if(!empty($matches)){
                        
                        $arrayResponse['nbJoueursMin']=$matches[1];
                        $arrayResponse['nbJoueursMax']=$matches[2];
                    }
                    else{

                        preg_match('/<div>(\d)/',$arrayContent[$a+2],$matches);
                        if(!empty($matches)){
                            $arrayResponse['nbJoueursMin']=$matches[1];
                            $arrayResponse['nbJoueursMax']=$matches[1];
                        }

                        else{
                            $this->writeLog($name,'nbJoueurs');
                            break;
                        }
                        
                    }
                    
                    
                }

                //si la line = type
                if(preg_match($arrayPreg['types'],$line)){

                    if(empty($arrayResponse['types']))
                    {
                        //on crée le tableau des types
                        preg_match_all('/<div>((\w*[éà]*\s*)+)<\/div>/',$line,$arrayTypes);
                        if(!empty($arrayTypes)){

                            foreach($arrayTypes[1] as $types){

                                array_push($arrayResponse['types'],$types);
                                
                            }
                        }
                        else{

                            $this->writeLog($name,"types");
                            break;
                        }
                       
                    }
                }
                //si la line = theme

                if(preg_match($arrayPreg["themes"],$line)){

                   preg_match_all('/<div>((\w*[éèêàÉ]*\s*)*)/',$arrayContent[$a+1],$matches);
                   if(!empty($matches)){

                    $arrayResponse["themes"]=$matches[1];

                   }
                   else{
                    $this->writeLog($name,"themes");
                    break;
                   }
                }

                //si line = auteur
                if(preg_match($arrayPreg['auteurs'],$line)){

                    preg_match_all('/<div>((\w*[éáè]*\s*)*)<\/di/',$arrayContent[$a+1],$arrayAuteurs);
                    if(!empty($arrayAuteurs)){
                        foreach($arrayAuteurs[1] as $auteur){
                        //solution provisoire pour éviter d'avoir les résultats en double
                        if(!in_array($auteur,$arrayResponse['auteurs'])){

                            array_push($arrayResponse['auteurs'],$auteur);

                        }
                    }
                    }
                    else{

                        $this->writeLog($name,'auteurs');
                        break;
                    }
                    
                                       
                }
                //si la line = editeur
                if(preg_match($arrayPreg['editeur'],$line)){

                    preg_match_all('/<div>((\w*\s*[é\-èà]*)*)</',$arrayContent[$a+1],$matches);
                    if(!empty($matches[1])){
                        $arrayResponse['editeur']=$matches[1][0];
                    }
                    else{
                        $this->writeLog($name,"editeur");
                        break;
                    }
                }
                
                //si la line = dessinateur
                //j'ai eu une erreur quand même 
                if(preg_match($arrayPreg['dessinateurs'],$line)){

                    preg_match_all('/<div>((\w*[éèàëôä\'\.ïÉ]*\s*)+)/',$arrayContent[$a+1],$arrayDessinateurs);

                    if(!empty($arrayDessinateurs)){
                        foreach($arrayDessinateurs[1] as $dessinateur){
                        
                            $dessinateur=str_replace($decodeString,$replaceString,$dessinateur);
                   
                            if(!in_array($dessinateur,$arrayResponse['dessinateurs'])){

                                array_push($arrayResponse["dessinateurs"],$dessinateur);
                            }
                        }

                    }
                    else{

                        $this->writeLog($name,'dessinateurs');
                        break;
                    }
                    
                }
                
            
            }
            if(!$this->invalide){

                //dd('jeu invalide check the log');
           

            //sinon on persist et on flush le game
            $game->setImageUrl($arrayResponse['image']);
            $game->setSlug($this->sluggify($name));
            $game->setDuration((int)$arrayResponse['duration']);
            $game->setMinimumAge((int)$arrayResponse['age']);
            $game->setShortDescription($arrayResponse['shortDescription']);
            $game->setLongDescription($arrayResponse['longDescription']);
            $game->setPlayersMax((int)$arrayResponse['nbJoueursMax']);
            $game->setPlayersMin((int)$arrayResponse['nbJoueursMin']);
            $this->gameRepository->add($game,true);

            //on ajoute les types à la base si ils n'existent pas en base
            //on ajoute à la relation game_types
            foreach($arrayResponse['types'] as $name){

                    
                if(empty($this->typeRepo->findByName($name))){

                    $type=new Type;
                    $type->setName($name);
                    $type->setSlug($this->sluggify($name));
                    $this->typeRepo->add($type,true);
                    

                    
                }       
                                        
                $type=$this->typeRepo->findByName($name);
                foreach($type as $item)
                {
                    $game->addType($item,true);

                }
            }
            //on ajoute les auteurs à la base si ils n'existent pas en base
            //on ajoute à la relation game_auteurs

            foreach($arrayResponse['auteurs'] as $nameAuteur){

                if(empty($this->auteurRepo->findByName($nameAuteur))){

                    $auteur=new Author;
                    $auteur->setName($nameAuteur);
                    $this->manager->persist($auteur);

                }                               
                $auteur=$this->auteurRepo->findByName($nameAuteur);
               
                foreach($auteur as $item)
                {
                    $game->addAuthor($item,true);
                }
            }
            //on ajoute les dessinateurs à la base si ils n'existent pas en base
            //on ajoute à la relation game_dessinateurs    
            foreach($arrayResponse['dessinateurs'] as $nameDessinateur){

                $nameDessinateur=mb_convert_encoding($nameDessinateur,'UTF-8');
                if(empty($this->dessinateurRepo->findByName($nameDessinateur))){

                    $dessinateur=new Illustrator;
                    $dessinateur->setName($nameDessinateur);
                    $this->manager->persist($dessinateur);

                }        
                                   
                $dessinateur=$this->dessinateurRepo->findByName($nameDessinateur);
               
                foreach($dessinateur as $item)
                {
                    $game->addIllustrator($item,true);
                }
            } 
            
            //on ajoute les themes à la base si ils n'existent pas en base
            //on ajoute à la relation game_theme


             foreach($arrayResponse['themes'] as $nameTheme){

                $nameTheme=mb_convert_encoding($nameTheme,'UTF-8');
                if(empty($this->themeRepo->findByName($nameTheme))){

                    $theme=new Theme;
                    $theme->setName($nameTheme);
                    $theme->setSlug($this->sluggify($nameTheme));
                    $this->manager->persist($theme);

                }        
                                   
                $theme=$this->themeRepo->findByName($nameTheme);
               
                foreach($theme as $item)
                {
                    $game->addTheme($item,true);

                }
            }
                $nameEditor=$arrayResponse['editeur'];
                $nameEditor=mb_convert_encoding($nameEditor,'UTF-8');

                if(empty($this->editorRepo->findByName($nameEditor))){

                    $editor=new Editor;
                    $editor->setName($nameEditor);
                    $editor->setSlug($this->sluggify($nameEditor));
                    $this->manager->persist($editor);

                }        
                               
                $this->manager->flush();

                $editor=$this->editorRepo->findByName($nameEditor);
               
                foreach($editor as $item){

                    $game->setEditor($item);

                }
            
            //remplir le champs date 

            $game->setReleasedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury()));
            $this->gameRepository->add($game,true);
        }
            
            return $game;
        }
        //renvoie un tableau avec dans l'ordre le href, le nom, description
    public function getListGames(int $nbPagesToScrap) 
    {
        //mon tableau qui sera retourné


        for($gu=1;$gu<$nbPagesToScrap;$gu++){


            //on va chercher les noms des jeux sur nbPagesToScrap pages
            //on accede aux pages de ce site en particulier de la façon suivante
            //https://www.play-in.com/jeux_de_societe/recherche/?p=1, 2 etc ... 

            $urlToScrap=$this->url.$gu; // l'url de la page à scrap + le numéro de page
            $this->browser->request('GET',$urlToScrap);//la ressource browser

            //on crée un objet Response en string

            $contentPageString=($this->browser->getResponse())->getContent();

        
            //on ecrit dans un txt le string retourné par la ligne précédente
            file_put_contents($this->tmpFile,$contentPageString);
            //on récupére le contenu du txt sous la forme d'un tableau
            $arrayContent=file($this->tmpFile);
            //id en attendant la db
            $a=0;

            
            while($a<count($arrayContent)){
                // récupération du nom du jeu
            $gameTmp = new GameTmp;

                while(is_null($gameTmp->getName())){
                    
                    

                    if($this->crawler($arrayContent[$a],"href")){

                        $href=$this->crawler($arrayContent[$a],"href");
                        $gameTmp->setHref($href);
                    
                       
                    }

                    if($this->crawler($arrayContent[$a],'name'))
                    {

                        $name=$this->crawler($arrayContent[$a], "name");
                        $gameTmp->setName($name);
                        
                    }

                    $a++;
                    if($a >= count($arrayContent)){
                        break;
                    }
                }

                $gameTmp->setId($a);

                if(!is_null($gameTmp->getName()) && !is_null($gameTmp->getHref()) ){


                    $this->gameTmpRepo->add($gameTmp,true);
                    $this->flag++;
                }
           

            }
        }
            return True;
    }
//méthode qui inscrit dans var/log/log.txt les jeux qui ont rencontré une erreur lors de leur création
//et qui ne seront pas dans la base de données 

    private function writeLog(string $nomJeu,string $error){

        $this->invalide=TRUE;
        // $handle=fopen('../var/log/log.txt','a');
        // $message="Erreur déclenchée pour le jeu $nomJeu. $error non trouvé.".PHP_EOL;
        // fwrite($handle,$message );
        // fclose($handle);

    }

    private function sluggify(string $string){

        return strtolower(str_replace(' ','-',$string));
    }
}