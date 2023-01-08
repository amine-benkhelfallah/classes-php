<center><h1>Classe UserPDO / Connexion à la base de donnée avec PDO</h1></center>
<center><p style='color:red; font-size:30px'>WARNING<span style='color:BLACK; font-size:30px'> "Pour effectuer les tests, il faut décommenter les echos dans le fichier index.php "</span></p></center>

<?php
session_start();

class Userpdo { 
    private $id;
    private $login;
    private $password;
    private $email;
    private $firstname;
    private $lastname;
    private $bdd;
    //----------- PDO----------------__construct------------aucun Paramètres (mais peut enavoir)----------------------------
    //                            Est appelé automatiquement lors de l’initialisation devotre objet. Initialise les différents attributs de votre objet.
    public function __construct() {
        try {
            $this->bdd = new PDO("mysql:host=localhost;dbname=classes" , "root", "");
            // On définit le mode d'erreur de PDO sur Exception
            $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connexion réussie"; 
        }
        catch(PDOException $e){  //variable (e)$e =l'instance de la class PDOException
            //echo "Echec de la connexion : " . $e->getMessage();
            exit;
        }


        // Vérification de la connexion
        if (isset($_SESSION['user'])){
            $this->id = $_SESSION['user']['id'];
            $this->login = $_SESSION['user']['login'];
            $this->password = $_SESSION['user']['password'];
            $this->email = $_SESSION['user']['email'];
            $this->firstname = $_SESSION['user']['firstname'];
            $this->lastname = $_SESSION['user']['lastname'];
        }
    }
        //-------------------------register()-----Paramètres : $login, $password, $email, $firstname,$lastname----------------------------
        //Crée l’utilisateur en base de donnée dans la table “utilisateurs”.Retourne un tableau contenant l'ensemble des informations de ce même utilisateur.
    public function register($login, $password, $email, $firstname, $lastname){
        if($login !== "" && $password !== "" && $email !=="" && $firstname !=="" && $lastname !=="" ){
            // requête
            $req="SELECT * FROM utilisateurs where login = :login";

            
            $select = $this->bdd->prepare($req);

            // exécution de la requête avec liaison des paramètres
            $select-> execute(array(':login' => $login));

            // récupération du tableau
            $fetch_all = $select->fetchAll();

            if(count($fetch_all) === 0){ // si = 0 --> utilisateur disponible

                // hachage du mot de passe
                $password = password_hash($password, PASSWORD_DEFAULT);

                // requête pour ajouter l'utilisateur dans la base de données  :on peut utiliser les marqueurinterogatifs genre(?.?.?.?.?) mais ici on a preferé les marqueurs només
                $req2 = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";

             
                $insert = $this->bdd -> prepare($req2);

                // exécution de la requête avec liaison des paramètres
                $insert-> execute(array(
                    ':login' => $login,
                    ':password' => $password,
                    ':email' => $email,
                    ':firstname' => $firstname,
                    ':lastname' => $lastname));

                $affichage = "<p style='color:green; font-size:30px'>Inscription réussie</p>";
                return $affichage; 
            }
            else{
                $affichage = "<p style='color:red; font-size:30px'>Utilisateur déjà existant </p>";
                return $affichage; // utilisateur déjà existant
            }
        }
       
        
        // fermer la connexion
        $this->bdd = null;
    }
        //------------------------------------connect()--------Paramètres :$login, $password---------------------
        //    Connecte l’utilisateur, et donne aux attributs de la classe les valeurs correspondantes à celles de l’utilisateur connecté.   
    public function connect($login, $password){
        if(!$this->isConnected()){
            if($login !== "" && $password !== ""){
                // requête
                $req = "SELECT * FROM utilisateurs where login = :login";

                // préparation de la requête
                $select = $this->bdd->prepare($req);

                // exécution de la requête avec liaison des paramètres
                $select-> execute(array(':login' => $login));

                // récupération du tableau
                $fetch_all = $select->fetchAll();

                if(count($fetch_all) > 0){ // utilisateur existant
                    
                    // récupération du mot de passe avec ASSOC
                    $select-> execute(array(':login' => $login));
                    $fetch_assoc = $select->fetch(PDO::FETCH_ASSOC);
                    $password_hash = $fetch_assoc['password'];

                    if(password_verify($password, $password_hash)){
                        
                        // récupération des données pour les attribuer aux attributs
                        $this->id = $fetch_assoc['id'];
                        $this->login = $fetch_assoc['login'];
                        $this->password = $fetch_assoc['password'];
                        $this->email = $fetch_assoc['email']; 
                        $this->firstname = $fetch_assoc['firstname'];
                        $this->lastname = $fetch_assoc['lastname'];

                        $_SESSION['user']= [
                            'id' => $fetch_assoc['id'],
                            'login' => $fetch_assoc['login'],
                            'password' => $fetch_assoc['password'],
                            'email' => $fetch_assoc['email'],
                            'firstname' => $fetch_assoc['firstname'],
                            'lastname' => $fetch_assoc['lastname']
                        ];
                        $affichage = "<p style='color:green; font-size:30px'> Connexion réussie </p>"; 
                        
                        return $affichage; 
                    }
                    else{
                        $affichage = "<p style='color:green; font-size:30px'>Mot de passe incorrect</p>"; 
                        return $affichage; 
                    }
                }
                else{
                    $affichage = "<p style='color:green; font-size:30px'>login incorrect</p>";
                    return $affichage;
                    
                }
            }
     
            // fermer la connexion
            $this->bdd = null;
        }
    }
        //------------------------------------disconnect()--------Paramètres aucun---------------------
        //                                       Déconnecte l’utilisateur  
    public function disconnect(){
            session_unset();
            session_destroy();

            $affichage = "<p style='color:green; font-size:30px'>Déconnexion réussie</p>";
            return $affichage; 
    
    }
        //------------------------------------delete()--------Paramètres aucun---------------------
        //                                   Supprime ET déconnecte un user  
    public function delete(){
       
        if($this->isConnected()){
            // requête pour supprimer l'utilisateur dans la base de données
            $req = "DELETE FROM utilisateurs WHERE id = :id";
            // préparation de la requête
            $delete = $this->bdd->prepare($req);
            // exécution de la requête avec liaison des paramètres
            $delete-> execute(array(':id' => $this->id));

            $this->disconnect();
            $affichage = "<p style='color:green; font-size:30px'>Suppression et deconnexion réussies</p>";
            return $affichage; 
        }
        else{
            $affichage = "<p style='color:red; font-size:30px'>Vous n'êtes pas connecté, vous devez être connecté pour supprimer le compte</p>";
            return $affichage; // utilisateur non connecté
        }
        // fermer la connexion
        $this->bdd = null;
    }
        //-------------------------------update()--------Paramètres: : $login, $password, $email, $firstname,$lastname---------------
        //                        Met à jour les attributs de l’objet, et modifie les informations en base de données.  
    public function update($login, $password, $email, $firstname, $lastname){
       
        if($this->isConnected()){
            //vérification que les champs ne sont pas vides
            if($login !== "" && $password !== "" && $email !=="" && $firstname !=="" && $lastname !=="" ){

                $password = password_hash($password, PASSWORD_DEFAULT);

                // requête pour vérifier que le login choisi n'est pas déjà utilisé
                $req = "SELECT * FROM utilisateurs where login = :login";

                // préparation de la requête
                $select = $this->bdd->prepare($req);

                // exécution de la requête avec liaison des paramètres
                $select-> execute(array(':login' => $login));

                // récupération du tableau
                $fetch_all = $select->fetchAll();

                if(count($fetch_all) === 0){ // login disponible
                    // récupération des données pour les attribuer aux attributs
                    $_SESSION['user']= [
                        'id' => $this->id,
                        'login' => $login,
                        'password' => $password,
                        'email' => $email,
                        'firstname' => $firstname,
                        'lastname' => $lastname
                    ];

                    // requête pour modifier l'utilisateur dans la base de données
                    $req2 = "UPDATE utilisateurs SET login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";
                    // préparation de la requête
                    $update = $this->bdd->prepare($req2);
                    // exécution de la requête avec liaison des paramètres
                    $update-> execute(array(
                        ':id' => $this->id,
                        ':login' => $login, 
                        ':password' => $password, 
                        ':email' => $email, 
                        ':firstname' => $firstname, 
                        ':lastname' => $lastname));

                    $affichage =   "<p style='color:green; font-size:30px'>Modification réussie</p>";
                    return $affichage; 
                }
              
            }
          
        }
        else{
            $affichage = "<p style='color:red; font-size:30px'>Vous n'êtes pas connecté, vous devez être connecté pour modifier le compte</p>";
            return $affichage; // utilisateur non connecté
        }
    }
        //------------------------------------isConnected()--------Paramètres : aucun---------------------
       //                              Retourne un booléen (true ou false) permettant de savoir si un utilisateur est connecté ou non 
    public function isConnected(){
        if($this->id !== null && $this->login !== null && $this->password !== null && $this->email !== null && $this->firstname !== null && $this->lastname !== null){
            return true; // utilisateur connecté
        }
        else{
            return false; // utilisateur non connecté
        }
    }
         //-----------------------------getAllinfos()--------Paramètres : aucun--------------------------
        //                           Retourne tableau contenant l'ensemble des informations de l'utilisateurs 
    public function getAllInfos(){
       
        if($this->isConnected()){
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
                        <td><?= $this->login; ?></td>
                        <td><?= $this->email; ?></td>
                        <td><?= $this->firstname; ?></td>
                        <td><?= $this->lastname; ?></td>
                    </tr>
            </table>
            <?php
        }
        else{
            $affichage = "Il faut se connecter pour avoir accès aux informations du compte. <br>";
            return $affichage; 
        }
    }
        //-----------------------------getLogin()--------Paramètres : aucun--------------------------
        //                           Retourne le login del’utilisateur Récupération de login 
    public function getLogin(){
       
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>Login de l’utilisateur est :<span style='color:red; font-size:30px'> <?= $this->login; ?></span></p>
                <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au login du compte. <br>";
            }
    }
        //-----------------------------getEmail()--------Paramètres : aucun--------------------------
        //                          Retourne le email del’utilisateur (Récupération du l'email)       
    public function getEmail(){
       
        if($this->isConnected()){
            ?>
           <p style='color:black; font-size:30px'>L'email del’utilisateur est : <span style='color:red; font-size:30px'><?= $this->email; ?></span></p>
                <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès a l'email du compte. <br>";
        }
    }
        //-----------------------------getFirstname()--------Paramètres : aucun--------------------------
        //                          Retourne le firstname del’utilisateur (Récupération du login)      
    public function getFirstname(){
       
        if($this->isConnected()){
            ?>
            <p style='color:black; font-size:30px'>Firstname de l'utilisateur est :<span style='color:red; font-size:30px'> <?= $this->firstname; ?></span></p>
                <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au prénom du compte. <br>";
        }
    }
        //-----------------------------getLastname()--------Paramètres  :aucun--------------------------
        //                          Retourne le lastname del’utilisateur (Récupération du lastname)    
    public function getLastname(){
       
        if($this->isConnected()){
            ?>
           <p style='color:black; font-size:30px'>Lastname de l’utilisateur est :<span style='color:red; font-size:30px'> <?= $this->lastname; ?></span></p>
                <?php
        }
        else{
            echo "Il faut se connecter pour avoir accès au nom du compte. <br>";
        }
    }
}
?>