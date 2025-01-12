<?php

class Mail extends Dbh {

    protected function setMail($id_receiver, $id_sender,$msg,$name_sender,$name_receiver,$dep,$arr,$msg_type,$id_trajet){

        $stmt = $this->connect()->prepare('SELECT * FROM messagerie WHERE id_trajet_msg = ? ');
        $result = $stmt->execute(array($id_trajet));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($id_sender == $user["id_sender"] && $id_receiver = $user["id_sender"] && $msg_type =  $user["msg_type"] ){
            header("location: ../../confirmation.php?action=alreadyhavethistrip");
            exit();
        }
        $stmt=NULL;
        $user=NULL;

        $email;

        $stmt = $this->connect()->prepare('INSERT INTO messagerie (id_receiver, id_sender, full_message, msg_type, name_sender,name_receiver,trip_depart,trip_arrival,id_trajet_msg) VALUES (?,?,?,?,?,?,?,?,?)');
        $result = $stmt->execute(array($id_receiver,$id_sender,$msg, $msg_type, $name_sender,$name_receiver,$dep,$arr,$id_trajet));
        $stmt->debugDumpParams();
        echo "<br>";
        echo "<br>";
 
        if($result==false){
            $stmt = null; //delete the statement
   
            header("location: ../../confirmation.php?action=stmtFailed");
            exit();
        }
        $stmt = null;

        $stmt = $this->connect()->prepare('SELECT email FROM utilisator WHERE id_user = ? ');
        $result = $stmt->execute(array($id_receiver));
        $stmt->debugDumpParams();
        echo "<br>";

        if($result==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $user["email"];
        // ---------------MAIL---------------------------------------------------------------------------------------
        $to  = $email; // notez la virgule
        $subject = 'BlaBlaCampus demande de reservation';
        
        // message
        $message = '<html><head><title>BlaBlaCampus</title></head><body>
        <div><p>Bonjour <span>'.$name_receiver.'</span></p>
                        <p>Je souhaiterai réserver une place dans ta voiture pour le trajet <span>'.$dep.'-'.$arr.'</span></p>
                        <p>En te remerciant.</p>
                    </div>
        </body></html>';
        
        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        
        // En-têtes additionnels
        $headers[] = 'To:  <'.$email.'>';
        $headers[] = 'From: '.$name_sender.'';
        //  $headers[] = 'Cc: anniversaire_archive@example.com';
        //  $headers[] = 'Bcc: anniversaire_verif@example.com';
        
        // Envoi
        mail($to, $subject, $message, implode("\r\n", $headers));
        // ---------------END MAIL---------------------------------------------------------------------------------------



    }

    protected function deleteMsg($id){

        $stmt = $this->connect()->prepare('DELETE FROM messagerie WHERE id_msg = ?');
        $result = $stmt->execute(array($id));

        if($result==false){
            $stmt = null; //delete the statement

            header("location: ../../confirmation.php?action=stmtFailed");
            exit();
        }
        $stmt = null;
    }

    protected function findMail($user_id){
        
        $stmt = $this->connect()->prepare('SELECT * FROM messagerie INNER JOIN utilisator ON messagerie.id_sender = utilisator.id_user WHERE id_receiver = ?;');
        $resultCheck=$stmt->execute(array($user_id));
        $stmt->debugDumpParams();

        if($resultCheck==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rc = $stmt->rowCount();
            if($rc==0){
                $stmt = null;
                header("location: ../../confirmation.php?action=noMails");
                exit();
            }
            // session_start();
            for ($i=0; $i <$rc ; $i++) { 
                $_SESSION["id-sender".$i] = $user[$i]["id_sender"];
                $_SESSION["full-message".$i] = $user[$i]["full_message"];
                $_SESSION["msg-type".$i] = $user[$i]["msg_type"];
                $_SESSION["name-sender".$i] = $user[$i]["name_sender"];
                $_SESSION["image-sender".$i] = $user[$i]["avatar"];
                $_SESSION["trip_depart".$i] = $user[$i]["trip_depart"];
                $_SESSION["trip_arrival".$i] = $user[$i]["trip_arrival"];
                $_SESSION["id_trajet_inc_msg".$i] = $user[$i]["id_trajet_msg"];

                $a=$_SESSION["id_trajet_inc_msg".$i];

            }
            $_SESSION["mail-rc"] = $rc;
            $_SESSION["id_receiver"] = $user[0]["id_receiver"];

            $stmt = null;
            
        }
        
    }

    protected function findMailTwo($user_id,$msg_type){
        $_SESSION["reserve_rc"] = 0;
        
        $stmt = $this->connect()->prepare('SELECT * FROM trajet INNER JOIN messagerie ON messagerie.id_receiver = trajet.id_utilsateur WHERE id_receiver =? AND msg_type =?;');
        $resultCheck=$stmt->execute(array($user_id,$msg_type));

        if($resultCheck==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rc = $stmt->rowCount();
            if($rc==0){
                $stmt = null;
                header("location: ../../confirmation.php?action=noMailsResevation");
                exit();
            }
            // session_start();
            for ($i=0; $i <$rc ; $i++) { 
                
                $_SESSION["reserve_calendar".$i] = $user[$i]["calendar"];
                $_SESSION["reserve_depart".$i] = $user[$i]["trip_depart"];
                $_SESSION["reserve_arrival".$i] = $user[$i]["trip_arrival"];
                $_SESSION["reserve_rtype".$i] = $user[$i]["routetype"];
                $_SESSION["reserve_idmsg".$i] = $user[$i]["id_msg"];
            }
            $_SESSION["reserve_rc"] = $rc;

            $stmt = null;
            
        }
        
    }

    protected function updateMessage($tr){
        $id_receiver;
        $id_sender;
        $msg_type;
        $name_sender;
        $name_receiver;
        $guest_number;
        $emailx;
        $depx;
        $arrx;

        $stmt = $this->connect()->prepare('SELECT * FROM messagerie WHERE id_trajet_msg = ?;');
        $resultCheck=$stmt->execute(array($tr));
        echo "<br>";
        $stmt->debugDumpParams();
        echo "<br>";
        if($resultCheck==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rc = $stmt->rowCount();
            if($rc==0){
                $stmt = null;
                header("location: ../../confirmation.php?action=noMails");
                exit();
            }
            // session_start();
            $id_receiver = $user[0]["id_receiver"];
            $id_sender = $user[0]["id_sender"];
            $msg_type = $user[0]["msg_type"];
            $name_sender = $user[0]["name_sender"];
            $name_receiver = $user[0]["name_receiver"];

            $stmt = null;
            
        }
        $id_receiver2 = $id_sender;
            $id_sender2 = $id_receiver;
            $msg_type2 = "Validation";
            $name_sender2 = $name_receiver;
            $name_receiver2 = $name_sender;

        $stmt = $this->connect()->prepare('UPDATE messagerie SET id_receiver = ?, id_sender=?, msg_type=?, name_sender=?, name_receiver=? WHERE id_trajet_msg = ?');
        $resultCheck=$stmt->execute(array($id_receiver2,$id_sender2,$msg_type2,$name_sender2,$name_receiver2,$tr));
        echo "<br>";
        $stmt->debugDumpParams();
        echo "<br>";

        if($resultCheck==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            $stmt = null;
        }

        $stmt = $this->connect()->prepare('SELECT * FROM trajet WHERE id_trajet = ?');
        $resultCheck=$stmt->execute(array($tr));
        echo "<br>";
        $stmt->debugDumpParams();
        echo "<br>";
        if($resultCheck==false){
            $stmt = null; //delete the statement

            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rc = $stmt->rowCount();
            if($rc==0){
                $stmt = null;
                header("location: ../../confirmation.php?action=noMails");
                exit();
            }
            
            $guest_number = $user[0]["guest"];
            $guest_number = $guest_number - 1;
            $depx = $user[0]["depart"];
            $arrx = $user[0]["arriver"];
          
            $stmt = null;
            
        }

        $stmt = $this->connect()->prepare('UPDATE trajet SET guest=? WHERE id_trajet = ?');
        $resultCheck=$stmt->execute(array($guest_number, $tr));
        echo "<br>";
        $stmt->debugDumpParams();
        echo "<br>";
        if($resultCheck==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            $stmt = null;
        }
        
        $stmt = $this->connect()->prepare('SELECT * FROM utilisator WHERE id_user = ? ');
        $result = $stmt->execute(array($id_receiver2));
        echo "<br>";
        $stmt->debugDumpParams();
        echo "<br>";

        if($result==false){
            $stmt = null; //delete the statement
            header("location: ../../confirmation.php?action=stmtFailedMailChecked");
            exit();
        }else{
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $emailx = $user["email"];
            
            // ---------------MAIL---------------------------------------------------------------------------------------
            $to  = $emailx; // notez la virgule
            $subject = 'BlaBlaCampus Validation de reservation';
            
            // message
            $message = '<html><head><title>BlaBlaCampus</title></head><body>
            <div><p>Bonjour <span>'.$name_receiver2.'</span></p>
                            <p>Je t\'informe qu\'une place t\'attend dans ma voiture pour le <span>'.$depx.'-'.$arrx.'</span></p>
                            <p>À tout bientôt.</p>
                        </div>
            </body></html>';
            
            // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            
            // En-têtes additionnels
            $headers[] = 'To:  <'.$emailx.'>';
            $headers[] = 'From: '.$name_sender2.'';
            //  $headers[] = 'Cc: anniversaire_archive@example.com';
            //  $headers[] = 'Bcc: anniversaire_verif@example.com';
            
            // Envoi
            mail($to, $subject, $message, implode("\r\n", $headers));
            // ---------------END MAIL---------------------------------------------------------------------------------------
            

        }
       
    }

}