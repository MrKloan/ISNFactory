
    <section class="jumbotron background1">
        <div class="container titre">
            <h1>E-mails</h1>
        </div>
    </section>
       
      
    <?php
        $mails = $this->dispatcher->model->getEmails();
    ?>
    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="panel panel-default">
                <div class="panel-heading">Mails reçus</div>
                <div class="receive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Emetteur</th>
                                <th>Objet</th>
                                <th>Date de réception</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($mails) == 0)
                                    echo("<tr><td colspan=\"3\">Aucune entrée.</td></tr>");
                                else
                                    for($i=0; $i < count($mails); $i++){
                                        $user_from = $this->dispatcher->model->getUser($mails[$i]->from);
                                        echo("<tr data-toggle=\"modal\" data-target=\"#msg_modal_".$i."\"><td>".$user_from->firstname." ".$user_from->lastname."</td><td>".$mails[$i]->subject."</td><td>".date("d/m/Y H:i:s",strtotime($mails[$i]->sended_at))."</td></tr>");
                                    }      
                            ?>
                        </tbody>   
                    </table>
                </div>
            </div>

            <?php for($i=0; $i < count($mails); $i++): ?>
                <?php $user_from = $this->dispatcher->model->getUser($mails[$i]->from); ?>
                <div class="modal fade" id="msg_modal_<?php echo($i); ?>" tabindex="-1" role="dialog" aria-labelledby="msg_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <div class="modal-title" id="msg_label">  
                                    <h4>Objet : <?php echo($mails[$i]->subject); ?></h4>
                                    <h5>Emetteur : <?php echo($user_from->firstname." ".$user_from->lastname); ?></h5>  
                                </div>                          
                            </div>
                            <div class="modal-body">
                                <?php echo(htmlspecialchars_decode($mails[$i]->content)); ?>
                            </div>
                            <div class="modal-footer">
                                <form action="" method="POST">
                                    <!--<input type="submit" class="btn btn-primary" value="Répondre"/>-->
                                    <input type="hidden" name="mail_id" value="<?php echo(base64_encode(SALT.$mails[$i]->id.PEPPER)); ?>" />
                                    <button type="submit" name="submit_del_mail" class="btn btn-danger">Supprimer</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        
            <hr/>
            
            <?php
                $receivers = $this->dispatcher->model->getProfReceivers();
            ?>
            <div class="jumbotron background2 panel panel-default newmail">
                <div id="head">Nouveau Mail</div>
                <form action="" method="POST">                           
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-2 col-md-2 space">
                                <label for="object_input" class="control-label" id="objet">Objet :</label>
                            </div>
                            <div class="col-xs-10 col-md-10 space">
                                <input type="text" name="object_mail" class="form-control" id="object_input" placeholder="Objet" required>
                            </div>
                         </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 space">
                                <select name="dest[]" class="form-control" multiple>
                                    <?php
                                        foreach ($receivers as $grade => $students)
                                        {
                                            echo("<optgroup label=\"".$grade."\">");
                                            for($i=0; $i < count($students); $i++)
                                               echo("<option>".$students[$i]->firstname." ".$students[$i]->lastname."</option>");
                                            echo("</optgroup>");
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 space">
                                <textarea class="form-control" name="content_mail" rows="5"></textarea>
                            </div>
                        </div>
                        <input type="submit" name="submit_mail" class="btn btn-primary" value="Envoyer"/> 
                    </div>                
                </form>
            </div>
        </div>
	</section>