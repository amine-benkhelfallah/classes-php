<center><h1>Classe User / Connexion à la base de donnée avec mysqli.</h1></center>   
<center><p style='color:red; font-size:30px'>WARNING<span style='color:BLACK; font-size:30px'> "Pour effectuer les tests, il faut décommenter les echos dans le fichier index.php "</span></p></center>
<?php
session_start();

class User{
    private $id;
    public $login;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    public $bdd;
    //-----------poo----------------__construct------------aucun Paramètres (mais peut enavoir)----------------------------
    //         Est appelé automatiquement lors de l’initialisation devotre objet. Initialise les différents attributs de votre objet.
    public function __construct(){
        $this->bdd = new mysqli('localhost','root','','classes');  
    }
    //-------------------------register()-----Paramètres : $login, $password, $email, $firstname,$lastname----------------------------
    //             Crée l’utilisateur en base de donnée dans la table “utilisateurs”.Retourne un tableau contenant l'ensemble des informations de ce même utilisateur.
    public function register($login, $password, $email, $firstname, $lastname){
        if($login !== "" && $password !== "" && $email !=="" && $firstname !=="" && $lastname !=="" ){
            $requete = "SELECT count(*) FROM utilisateurs where login = '$login'";
            $exec_requete = $this->bdd -> query($requete);
            $reponse      = mysqli_fetch_assoc($exec_requete);
            $count = $reponse['count(*)'];
                if($count==0){

                    // requête pour ajouter l'utilisateur dans la base de données
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $requete2 = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES ('$login', '$password', '$email', '$firstname', '$lastname')";
                    $exec_requete2 = $this->bdd -> query($requete2);
                    $affichage = "Inscription réussie";
                    return $affichage; // inscription réussie
                }
                else{
                    $affichage = "Utilisateur déjà existant";
                    return $affichage; 
                }
            }
            mysqli_close($this->bdd); 
    }
    //------------------------------------connect()--------Paramètres :$login, $password---------------------
    //                 Connecte l’utilisateur, et donne aux attributs de la classe les valeurs correspondantes à celles de l’utilisateur connecté.   
    public function connect($login, $password){
            $login = mysqli_real_escape_string($this->bdd,htmlspecialchars($login));
            $password = mysqli_real_escape_string($this->bdd,htmlspecialchars($password));

            if($login !== "" && $password !== ""){
                $requete = "SELECT count(*) FROM utilisateurs where login = '$login'";
                $exec_requete = $this->bdd -> query($requete);
                $reponse      = mysqli_fetch_assoc($exec_requete);
                $count = $reponse['count(*)'];

                    if($count!=0){
                        $requete2 = "SELECT * FROM utilisateurs where login = '$login'";
                        $exec_requete2 = $this->bdd -> query($requete2);
                        $reponse2      = mysqli_fetch_assoc($exec_requete2);
                        $password_hash = $reponse2['password'];

                                if(password_verify($password, $password_hash)){
                                    
                                    // récupération des données pour les attribuer aux attributs
                                    $this->id = $reponse2['id'];
                                    $this->login = $reponse2['login'];
                                    $this->password = $reponse2['password'];
                                    $this->email = $reponse2['email']; 
                                    $this->firstname = $reponse2['firstname'];
                                    $this->lastname = $reponse2['lastname'];
                                    $_SESSION['user']= [
                                        'id' => $reponse2['id'],
                                        'login' => $reponse2['login'],
                                        'password' => $reponse2['password'],
                                        'email' => $reponse2['email'],
                                        'firstname' => $reponse2['firstname'],
                                        'lastname' => $reponse2['lastname']
                                    ];
                                    $affichage = "Connexion réussie";
                                    return $affichage; 
                                }
                    else{
                        $affichage = "Mot de passe incorrect";
                        return $affichage; 
                    }
                }
                else{
                    $affichage = "login incorrect";
                    return $affichage; 
                }
            }
            
            mysqli_close($this->bdd); // fermer la connexion
    }
    //------------------------------------disconnect()--------Paramètres aucun---------------------
   //                                  Déconnecté l’utilisateur  
    public function disconnect(){
         
        session_unset();
        session_destroy();
        
        $affichage = "Vous étes déconnecté";
        return $affichage; 

    }
      //------------------------------------delete()--------Paramètres aucun---------------------
      //                                  Supprime ET déconnecte un user  
    public function delete(){
        
        if($this->isConnected()){
            // requête pour supprimer l'utilisateur dans la base de données
            $requete = "DELETE FROM utilisateurs WHERE id = '$this->id'";
            $this->bdd -> query($requete);
            $this->disconnect();
            $affichage = "Suppression et deconnexion réussies";
            return $affichage; 

            mysqli_close($this->bdd); // fermer la connexion
        }
        else{
            $affichage = "Il faut se connecter pour pouvoir supprimer le compte";
            return $affichage; 
        }
    }
        
    //------------------------------------update()--------Paramètres: : $login, $password, $email, $firstname,$lastname------------
    //                            Met à jour les attributs de l’objet, et modifie les informations en base de données. 
    public function update($login, $password, $email, $firstname, $lastname){
        
        if($this->isConnected()){
                // requête pour modifier l'utilisateur dans la base de données      
                $requete = "UPDATE utilisateurs SET login = '$login', password = '$password', email = '$email', firstname = '$firstname', lastname = '$lastname' WHERE id = '$this->id'";
                        $this->bdd -> query($requete);

                        $affichage = "Modification réussie";
                        return $affichage; 
        }  
        else{
            $affichage = "Il faut se connecter pour pouvoir modifier le compte";
            return $affichage; 
        }
    }
    //------------------------------------isConnected()--------Paramètres : aucun---------------------
    //Retourne un booléen (true ou false) permettant de savoir si un utilisateur est connecté ou non 
    public function isConnected(){
        if($this->id !== null && $this->login !== null && $this->password !== null && $this->email !== null && $this->firstname !== null && $this->lastname !== null){
            return true; 
        }
        else{
            return false; 
        }
    }
    //-----------------------------getAllinfos()--------Paramètres : aucun--------------------------
    //                  Retourne tableau contenant l'ensemble des informations de l'utilisateurs 
    public function getAllInfos(){ 
        if($this->isConnected()){
            // requête pour récupérer les données de l'utilisateur dans la base de données
            $requete = "SELECT * FROM utilisateurs WHERE id = '$this->id'";
            $exec_requete = $this->bdd -> query($requete);
            $reponse = mysqli_fetch_assoc($exec_requete);

            //affichage
            ?>
            <table>
                <thead>
                <tr style='color:white; font-size:30px; background-color:black;'>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                </tr>
                </thead>
                <tbody>
                <tr style='color:white; font-size:30px; background-color:#C0C0C0;'>
                    <td><?= $reponse['login']; ?></td>
                    <td><?= $reponse['email']; ?></td>
                    <td><?= $reponse['firstname']; ?></td>
                    <td><?= $reponse['lastname']; ?></td>
                </tr>
                </table>
            <?php
        }
        else{
            $affichage = "Il faut se connecter pour avoir accès aux informations du compte <br>";
            return $affichage; 
        }
    }  
    //-----------------------------getLogin()--------Paramètres : aucun--------------------------
    //                            Retourne le login del’utilisateur Récupération de login 
    public function getLogin(){
        
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>Login de l’utilisateur est :<span style='color:red; font-size:30px'> <?= $this->login; ?></span></p>
            <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au login du compte <br>";
        }
    }
    //----------------------------getEmail()--------Paramètres : aucun--------------------------
    //    Retourne le email del’utilisateur (Récupération du l'email)       
    public function getEmail(){
        
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>L'email del’utilisateur est : <span style='color:red; font-size:30px'><?= $this->email; ?></span></p>
            <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès a l'email du compte <br>";
        }
    }
       
    //-----------------------------getFirstname()--------Paramètres : aucun--------------------------
    //                           Retourne le firstname del’utilisateur (Récupération du login)      
    public function getFirstname(){
        
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>Firstname de l'utilisateur est :<span style='color:red; font-size:30px'> <?= $this->firstname; ?></span></p>
            <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au prénom du compte <br>";
        }
    }   
    //-----------------------------getLastname()--------Paramètres  :aucun--------------------------
    //                  Retourne le lastname del’utilisateur (Récupération du lastname)    
    public function getLastname(){
        
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>Lastname de l’utilisateur est :<span style='color:red; font-size:30px'> <?= $this->lastname; ?></span></p>
            <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au nom du compte <br>";
        }
    }

}             
?>
