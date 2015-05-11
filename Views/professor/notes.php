	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Notes</h1>
        </div>
	</section>

    <section class="jumbotron background3">
        <div class="container">
            <div class="panel-group list-group" id="notes">
              <div class="panel panel-default">
                <?php 
                    $grades= $this->dispatcher->model->getGrades(); 
                    for($i=0; $i<count($grades); $i++){
                        $works = $this->dispatcher->model->getProfWorks($grades[$i]);
                        if($works)
                            for($j=0; $j<count($works); $j++):
                ?>
                    <a class="list-group-item" data-toggle="collapse" data-parent="#notes" href="#<?php echo(sha1($grades[$i]->id."_".$works[$j]->id)); ?>"><?php echo($grades[$i]->grade." [".ucfirst($works[$j]->type)."] : ".$works[$j]->title); ?></a>
                    <div id="<?php echo(sha1($grades[$i]->id."_".$works[$j]->id)); ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-striped table-hover table-responsive">
                                <thead>
                                    <tr><th>Prénom</th><th>Nom</th><th>Coefficient</th><th>Note</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $students = $this->dispatcher->model->getStudentsByGrade($grades[$i]->id);
                                        if($students)
                                            for($k=0; $k<count($students); $k++)
                                            {
                                                $note = ($this->dispatcher->model->getNote($works[$j]->id, $students[$k]->id)) ? $this->dispatcher->model->getNote($works[$j]->id, $students[$k]->id)->note : "Non noté";
                                                echo("<tr><td>".$students[$k]->firstname."</td><td>".$students[$k]->lastname."</td><td>".$works[$j]->coeff."</td><td>".$note."</td><td><a title=\"Modifier\" class=\"icon-pencil update-note\" data-toggle=\"modal\" data-target=\"#update_note_modal_".sha1($works[$j]->id."_".$students[$k]->id)."\"></a></td></tr>");
                                            }
                                    ?>
                                </tbody>
                            </table>
                            <?php if($works[$j]->type != "projet"): ?>
                            <div class="list-group-item">
                                <form action="" method="POST">
                                    <input type="hidden" name="work_id" value="<?php echo(base64_encode(SALT.$works[$j]->id.PEPPER)); ?>" />
                                    <input type="submit" name="submit_del_work" class="btn btn-danger" value="Supprimer cette interrogation" />
                                </form>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                    <?php for($k=0; $k<count($students); $k++): ?>
                        <div class="modal fade" id="update_note_modal_<?php echo(sha1($works[$j]->id."_".$students[$k]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="update_note_label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="update_note_label">Modifier la note</h4>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="old_note_update">Ancienne note</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <?php $note = ($this->dispatcher->model->getNote($works[$j]->id, $students[$k]->id)) ? $this->dispatcher->model->getNote($works[$j]->id, $students[$k]->id)->note : ""; ?>
                                                    <input type="number" name="old_note_update" id="old_note_update" class="form-control" value="<?php echo($note); ?>" disabled />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="new_note_update">Nouvelle note</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" step="any" min="0" max="20" name="new_note_update" id="new_note_update" class="form-control" required/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="note_id" value="<?php echo(base64_encode(SALT.$works[$j]->id."_".$students[$k]->id.PEPPER)); ?>" />
                                            <input type="submit" name="submit_edit_note" class="btn btn-primary" value="Modifier"/>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
            <?php endfor; } ?>

                <div class="list-group-item">
                    <a href="#" data-toggle="modal" data-target="#new_note_modal" class="btn btn-primary">Ajouter une interrogation</a>
                </div>
              </div>

              <!-- Ajouter une interrogation -->
              <div class="modal fade" id="new_note_modal" tabindex="-1" role="dialog" aria-labelledby="new_note_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="new_note_label">Ajouter une interrogation</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="for_grade">Classe concernée</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select id="for_grade" class="form-control" name="for_grade">
                                                <?php
                                                    for($i=0; $i<count($grades); $i++)
                                                        echo("<option value=\"".$grades[$i]->id."\">".$grades[$i]->grade."</option>");
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="note_name">Nom de l'interrogation</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="note_name" id="note_name" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="coeff">Coefficient</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" name="coeff" id="coeff" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="date">Date</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="date" id="date" class="form-control datepicker"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_new_note" class="btn btn-primary" value="Ajouter"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="jumbotron background1">
        <div class="container titre">
            <h1>Configuration</h1>
        </div>
    </section>

    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="panel panel-default panel-trimesters">
                <form action="" method="POST">
                    <table class="table table-hover">
                        <thead>
                            <th class="col-md-6 col-sm-6 col-xs-6">Trimestres</th>
                            <th class="col-md-6 col-sm-6 col-xs-6">Date de début</th>
                        </thead>
                        <tbody>
                            <?php $trimesters = $this->dispatcher->model->getTrimesters(); ?>
                            <tr><td>1er Trimestre</td><td><input type="text" class="datepicker form-control" name="trimester1" value="<?php echo($trimesters != NULL ? $trimesters->trimester1 : ''); ?>" /></td></tr>
                            <tr><td>2ème Trimestre</td><td><input type="text" class="datepicker form-control" name="trimester2" value="<?php echo($trimesters != NULL ? $trimesters->trimester2 : ''); ?>" /></td></tr>
                            <tr><td>3ème Trimestre</td><td><input type="text" class="datepicker form-control" name="trimester3" value="<?php echo($trimesters != NULL ? $trimesters->trimester3 : ''); ?>" /></td></tr>
                        </tbody>
                    </table>
                    <div class="list-group-item">
                        <input type="submit" name="submit_trimesters" class="btn btn-primary" value="Envoyer" />
                    </div>
                </form>
            </div>
        </div>
    </section>