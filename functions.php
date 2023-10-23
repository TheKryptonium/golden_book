<?php

/****************************LES FONCTIONS ***************************************************************/
    function security($data){
        return trim(strip_tags(htmlentities($data)));
    }
   
    function ajout(array $composant,string $name,array $ingredient,int $total, array $data){
        if(isset($data[$name])){
            $value = $data[$name];
            if(is_array($value)){
                foreach($data[$name] as $value){
                    if(isset($composant[$value])){
                        $ingredient[]=$value;
                        $total+=$composant[$value];
                    }
                }
            }else{   
                if(isset($composant[$value])){
                    $ingredient[]=$value;
                    $total+=$composant[$value];
                }
            }
        }
    }

    function dump($variable){
        echo '<pre>';
        var_dump($variable);
        echo'</pre>';
    }

    /**********************LES CLASSES*******************************************/
    
    class Creneau{
        public $debut;
        public $fin;

        public function __construct(int $d,int $f){
            $this->debut = $d;
            $this->fin = $f;
        }
        public function includeHeure(int $heure):bool{
            return $heure>=$this->debut && $heure <=$this->fin;
        }
        public function in_creneau(Creneau $cren):bool{
            return ($cren->debut>$this->debut && $cren->fin<$this->fin)? true : false;
        }

        public function intersect(Creneau $cren):bool{
            return ($this->in_creneau($cren)||($cren->debut<$this->debut && $cren->fin>$this->fin)||($this->debut>$cren->debut && $this->fin>$cren->fin))? true : false;
        }
    }

    class Compteur{
        public string $file;

        public function increment():void{
            $vues = 1;
            if(file_exists($this->file)){
                $vues = (int)file_get_contents($this->file)+1;
                file_put_contents($this->file,$vues);
            }
            file_put_contents($this->file,$vues);
        }

        public function getVue(): int{
            return(file_exists($this->file))? (int)file_get_contents($this->file):0;
            
        }

        public function __construct(string $file){
            $this->file=$file;
        }

    }

    class Message{
        private string $username;
        private string $message;
        private $date;


        public function __construct(string $username, string $message, ?DateTime $date=null/*ça peut être un datetime ou null*/){
            $this->username = $username;
            $this->message=$message;
            $this->date = $date ?: new DateTime();
        }

        public function isValid() : bool{
          return empty($this->getErrors()); 
        }

        public function getErrors():array{
            $errors =[];
            if(strlen($this->username)<3){
                $errors["username"] = "Les informations concernant votre nom sont erronées";
            }
            if(strlen($this->message)<10){
                $errors["message"]="Votre message est erroné";
            }
            return $errors;
        }

        public function toHTML(): string{
            $username = htmlentities($this->username);
            $message = nl2br(htmlentities($this->message));
            $date = $this->date->format("d/m/Y à H:i");
            return <<<HTML
            <p>
                <strong>{$username}</strong>, <em>{$date}</em><br>
                {$message}
            </p>
HTML;
        }

        public function toJSON():string{
         return json_encode(
            [
                "username" => $this->username,
                "message" => $this->message,
                "date" => $this->date->getTimestamp()
            ]);

        }
        public static function FromJSON(string $line):Message{
            $data = json_decode($line,true);/*Obtenir une tableau associatif contenant le contenu décodé */
            $date = new DateTime("@".$data['date']);
            $date->setTimeZone(new DateTimeZone('Africa/Douala'));
            return new self($data['username'], $data['message'],$date);
        }
    }

    class GuestBook{
        private string $file;
        
        public function __construct(string $file){
           $directory = dirname($file);
           if(!is_dir($directory)){
                mkdir($directory,0777,true);
           }
           if(!file_exists($file)){
                touch($file);
           }
           $this->file=$file;

        }

        public function addMessage(Message $message){
            file_put_contents($this->file, $message->toJSON(). PHP_EOL, FILE_APPEND);
        }

        public function getMessages():array{
            $content = trim(file_get_contents($this->file));
            $lines = explode(PHP_EOL, $content);
            $messages=[];
            foreach($lines as $line){
                $messages[] = Message::FromJSON($line);
            }
            return array_reverse($messages); /*Obtenir les messages du plus récent au plus vieux*/
        }
    }

    class Post{
        public $id;
        public $name;
        
        public function getExcerpt(){
            return $this->name;
        }
    }