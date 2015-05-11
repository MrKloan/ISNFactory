	<section class="jumbotron background1">
		<div class="container titre">
            <h1>FAQ</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container">
            <div id="faq">
                <?php 
                    $faq = $this->dispatcher->model->getProfFAQ();
                    
                    if(count($faq) == false)
                        echo("<div><h2>Aucune entrée</h2></div>");
                    else
                        for($i=0; $i < count($faq); $i++)
                            echo("<form action=\"\" method=\"POST\"><div><h2>".$faq[$i]->question."<a data-toggle=\"modal\" data-target=\"#edit_modal_".sha1($faq[$i]->id)."\" class=\"btn btn-primary\">Modifier</a><input type=\"hidden\" name=\"faq_id\" value=\"".base64_encode(SALT.$faq[$i]->id.PEPPER)."\"/><button type=\"submit\" name=\"submit_del_faq\" class=\"btn btn-danger\">Supprimer</button></form></h2>".htmlspecialchars_decode($faq[$i]->answer)."<hr /></div>");
                ?>
                <div><a data-toggle="modal" data-target="#new_modal" class="btn btn-warning">Créer une entrée</a></div>

                <!-- Ajouter une entrée -->
                <div class="modal fade" id="new_modal" tabindex="-1" role="dialog" aria-labelledby="new_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="new_label">Créer une entrée dans la FAQ</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="question_new">Question</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="question_new" id="question_new" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <textarea name="answer_new" id="answer_new" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_faq_create" class="btn btn-primary" value="Créer"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modifier une question -->
                <?php
                    for($i=0; $i < count($faq); $i++):
                ?>
                    <div class="modal fade" id="edit_modal_<?php echo(sha1($faq[$i]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="edit_label_<?php echo(sha1($faq[$i]->id)); ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="edit_label_<?php echo(sha1($faq[$i]->id)); ?>">Modifier la question</h4>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="question_edit">Question</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="question_edit" id="question_edit" class="form-control" value="<?php echo($faq[$i]->question); ?>" required/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <textarea name="answer_edit" id="answer_edit_<?php echo($i); ?>" class="form-control" required><?php echo($faq[$i]->answer); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="faq_id" value="<?php echo(base64_encode(SALT.$faq[$i]->id.PEPPER)); ?>"/>
                                        <input type="submit" name="submit_faq_edit" class="btn btn-primary" value="Modifier"/>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>  
	</section>