<?php

    

    $title = "Le livre d'or";
    require './header.php';
    require_once './functions.php';
    $errors=null; $success = false;
    $guest = new GuestBook('messages.json');

    if(isset($_POST['username']) && isset($_POST['msg'])){
        $date = new DateTime();
        $message = new Message($_POST['username'], $_POST['msg'],$date);
        if($message->isValid()){
            $guest->addMessage($message);
            $success = true;
            $_POST=[];
        }else{
            $errors = $message->getErrors();
        }
        
    }
    $messages = $guest->getMessages();
    
?>

<h1 class="my-3 text-center">LE LIVRE D'OR</h1>
<div class="container">
    <?php if($success):?>
    <div class="row alert alert-success">Message envoyé</div>
    <?php endif?>

    <?php if(!empty($errors)) :?>
    <div class="row alert alert-danger">Les informations renseignees ne sont pas valides</div>
    <?php endif?>

    <form method="post" action="" class="my-3 w-2">
        <h2>Formulaire</h2>
        <div class="form-group">
            <label for="username">Username :</label>
            <input value="<?=htmlentities($_POST['username']??"")?>"  id="username" class="form-control <?=(isset($errors["username"]))? " is-invalid" : ""?>" type="text" name="username">
            <?php if(isset($errors["username"])) : ?>
                <div class="invalid-feedback"><?=$errors["username"]?></div>
            <?php endif?>
        </div>
        <div class="form-group my-4">
            <textarea  id="my-textarea" name="msg" placeholder="Entrez votre message" class="form-control <?=(isset($errors["message"]))? " is-invalid" : ""?>" ><?=htmlentities($_POST['msg']??"")?></textarea>
            <?php if(isset($errors["message"])) : ?>
                <div class="invalid-feedback"><?=$errors["message"]?></div>
            <?php endif?>
        </div>
       
        <button type="submit" class="btn btn-primary ">Envoyer</button>
    </form>
    <?php if(!empty($messages)) : ?>
    <div class="row">
        <h2>Vos commentaires</h2>
        <div class="mt-3">
            <?php for($j=0; $j<5; $j++) : ?>
                <?=$messages[$j]->toHTML()?>
            <?php endfor?>
        </div>
    </div>
    <?php endif?> 
    <!--<//?= dump($_POST)?>-->
</div>
