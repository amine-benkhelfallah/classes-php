<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POO /PDO</title>
</head>   

<body>
      
    <?php   
     //                   include 'user.php';      
     //                   $user = new User();

            /* #########        OU         ##########   */

                        include 'user-pdo.php';
                        $user = new Userpdo();


    //Test du register
    //echo $user->register('a', 'a', 'aminet@gmail.com', 'Amine', 'BEN') ."<br>";


    //Test du connect
    //echo $user->connect('a', 'a')."<br>";

    //Test du disconnect
     // echo $user->disconnect()."<br>";

    // Test du delete (pour lancer le test il faut créer un user avec "register"le connecter puis apres le suprimer )
      //echo $user->delete()."<br>";

    //Test du update (pour lancer le test il faut créer un user avec "register"le connecter puis apres le modifier )
   //echo $user->update('a', 'a', 'aminet@gmail.com', 'AMINE', 'BEN');

    // Test du isConnected
    //echo $user->isConnected();

    // Test du getAllInfos
    echo $user->getAllInfos();

     //Test du getLogin
    echo $user->getLogin();

    // Test du getEmail
    echo $user->getEmail();

    // Test du getFirstname
    echo $user->getFirstname();

    // Test du getLastname
     echo $user->getLastname();
    

    ?>
</body>
</html>