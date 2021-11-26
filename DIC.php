<?php

class DIC{

    // Cette variable sert à stocker les resolvers
    private $registry = [];

    // private $factories = [];

    // Cette variable sert à stocker les instances
    public $instances = [];

    // Cette methode permet de modifier les valeurs d'un resolver
    // Elle prend en parametre une clé (qui correspond à une classe) 
    // et un resolver qui contiendra une fonction d'appel
    public function set($key, Callable $resolver){

        $this->registry[$key] = $resolver;

    }

    // Cette methode permet de retourner à chaque appel, une nouvelle instance
    public function setFactory($key, Callable $resolver){

        $this->factories[$key] = $resolver;

    }


    // Permet de creer une nouvelle instance manuellement
    public function setInstance($instance){
        // On utilise la ReflectionClass pour pouvoir analyser l'objet
        // et avoir acces a des methode associer à cette classe
        $reflection = new ReflectionClass($instance);
        $this->instances[$reflection->getName()] = $instance;
    }

    // Cette methode permet de recuperer une instance en prenant
    // en parametre la clé qui correspond au nom d'une classe
    public function get($key){

        // if(isset($this->factories[$key])){
        //     return $this->factories[$key]();
        // }

        // Nous verifions si l'instance n'est pas déja presente 
        // dans le tableau des instances
        if(!isset($this->instances[$key])){
            // Si elle n'est pas présente,
            // Nous verifions si la clé est presente dans le registre
            if(isset($this->registry[$key])){
                // Si oui, le tableau d'instance recupere 
                // la valeur du tableau de registre
                $this->instances[$key] = $this->registry[$key]();
            }
            else{
                // Sinon, nous verifions si la clé est instanciable
                $reflected_class = new ReflectionClass($key);
                if($reflected_class->isInstantiable()){

                    // Nous recupèrons le constructeur associer a la clé
                    $constructor = $reflected_class->getConstructor();

                    // S'il existe
                    if($constructor){
                        // On récupère les parametres et on les stockes dans une variable
                        $parameters = $constructor->getParameters();

                        // Nous les stockons par la suite dans un tableau
                        $constructor_parameters = [];
                        // Pour chaque parametres,
                        foreach($parameters as $parameter){

                            // On verifie s'il y s'agit d'une classe ou non
                            if($parameter->getClass()){
                                // Si oui, on stocke son nom dans le tableau
                
                                $constructor_parameters[] = $this->get($parameter->getClass()->getName());
                            }
                            else{
                                // Sans quoi, on recupere l'attribut par defaut
                                $constructor_parameters[] = $parameter->getDefaultValue();
                            }
                        }
                        // On crée une nouvelle instance avec les arguments stocker dans le tableau
                        // (On utilise newInstanceArgs au lieu de newInstance pour faire passer un tableau)
                        $this->instances[$key] = $reflected_class->newInstanceArgs($constructor_parameters);
                    }
                    // Si la classe n'a pas de constructeur
                    else{
                        // On cree directement une nouvelle instance sans argument
                        $this->instances[$key] = $reflected_class->newInstance();
                    }
                }
                // Si la classe n'est pas instanciable,
                else{
                    // On affiche un message d'erreur
                    throw new Exception($key . " is not an instanciable Class");
                }

            }
        }
        // Pour finir, on retourne l'instance
        return $this->instances[$key];

    }

}