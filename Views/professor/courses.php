	<?php
        $diff = $this->dispatcher->model->getDiffGrades(); //Recupère les grades différents avec S1, S2 ... : Première S1
        //var_dump($diff);
        
        $tableau = array(array());
        for($i=0; $i < count($diff); $i++){
            foreach ($this->dispatcher->model->getIdGrades(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))) as $key => $value){
                $tableau[$i][] = $value->id; //Recupère les id des grades ayant un grade commencant par diff[i] sans S1, donc Première et Terminale
            }
        }
        //var_dump($tableau);
    ?>

    <?php
        for($i=0; $i < count($diff); $i++):
    ?>
        <section class="jumbotron background1">
    		<div class="container titre">
                <h1><?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?></h1>
            </div>
    	</section>

        <?php
            $courses = $this->dispatcher->model->getCoursesByGrades($tableau[$i]);
            $courses_for = $this->dispatcher->model->getCoursesFor($tableau[$i]);
        ?>
        <section class="jumbotron background3 groups">
            <div class="container">
                <div class="panel panel-default">
                    <table class="table table-hover">
                        <thead>
                            <th class="col-sm-9">Cours</th>
                            <th class="col-sm-1">Disponible</th>
                            <th class="col-sm-2"><span class="glyphicon glyphicon-plus ajout" data-toggle="modal" data-target="#cours_modal_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>"></span></th>
                        </thead>
                        <tbody>
                            <?php for($j=0; $j < count($courses); $j++): ?>
                                <form action="" method="POST">
                                    <tr>
                                        <?php
                                            if(!$courses)
                                                echo("<td colspan=\"3\">Aucun cours disponible.</td>");
                                            else{
                                        ?>
                                        <td>Chapitre <?php echo($courses[$j]->chapter); ?> : <?php echo($courses[$j]->title); ?></td>
                                        <td>
                                            <?php if($courses_for[0][$j]->enabled == 1): ?>
                                                <button type="submit" title="Disponibilite" class="btn btn-sm btn-success" name="submit_dispo_ok">Oui</button>
                                            <?php else: ?>
                                                <button type="submit" title="Disponibilite" class="btn btn-sm btn-danger" name="submit_dispo_ko">Non</button>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo(WEB_ROOT.'/extranet/professor/chapter/'.$courses[$j]->id); ?>" title="Visuel" class="glyphicon glyphicon-eye-open view-courses"></a>
                                            <a title="Modifier" class="icon-pencil update-courses" data-toggle="modal" data-target="#edit_modal_<?php echo(sha1($courses[$j]->id)); ?>"></a>
                                            <input type="hidden" name="course_id" value="<?php echo(base64_encode(SALT.$courses[$j]->id.PEPPER)); ?>" />
                                            <button type="submit" title="Supprimer" name="submit_course_del" class="btn-link icon-cancel remove-courses"></button>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                </form>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
        

                <div class="modal fade" id="cours_modal_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" tabindex="-1" role="dialog" aria-labelledby="cours_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <div class="modal-title" id="cours_label">  
                                    <h4>Ajout de cours</h4> 
                                </div>                          
                            </div>
                            <form class="form-horizontal" role="form" action="" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="chap" class="col-sm-3 control-label">Numéro</label>
                                        <div class="col-sm-9">
                                            <input type="number" name="number_course_create" class="form-control" id="chap" min="1">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="title" class="col-sm-3 control-label">Chapitre</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="name_course_create" class="form-control" id="title" placeholder="Nom du Chapitre">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_course_create" class="btn btn-primary" value="Ajouter"/>
                                    <input type="hidden" name="grades_course_create" value="<?php echo(base64_encode(serialize($tableau[$i]))); ?>">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php 
                    if($courses)
                        for($j=0; $j < count($courses); $j++): 
                ?>
                <div class="modal fade" id="edit_modal_<?php echo(sha1($courses[$j]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="edit_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <div class="modal-title" id="edit_label">  
                                    <h4>Edition</h4> 
                                </div>                          
                            </div>
                            <form class="form-horizontal" role="form" action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <h4>Ajout de cours</h4>
                                    <input type="file" name="course_file"/>

                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fichier</th>
                                                <th>Supprimer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $files = unserialize(base64_decode($courses[$j]->files_url));
                                                if($files)
                                                    for($k=0; $k < count($files); $k++)
                                                        echo("<tr><td>".substr($files[$k],strrpos($files[$k], "/")+1,strlen($files[$k]))."</td><td><form action=\"\" method=\"POST\" ><input type=\"hidden\" name=\"file_id\" value=\"".base64_encode(SALT.$courses[$j]->id."_".$k.PEPPER)."\"/><button type=\"submit\" name=\"submit_del_file\" class=\"btn btn-link icon-cancel remove-courses\"></a></form></td></tr>");
                                                else
                                                    echo("<tr><td colspan=\"2\">Aucune entrée.</td></tr>");
                                            ?>
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <label for="content" class="control-label">Ajouter du texte</label>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" name="course_edit_description" id="text_<?php echo($i."_".$j); ?>"><?php echo($courses[$j]->description); ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="course_directory" value="<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" />
                                    <input type="hidden" name="course_id" value="<?php echo(base64_encode(SALT.$courses[$j]->id.PEPPER)); ?>" />
                                    <input type="submit" name="submit_course_edit" class="btn btn-primary" value="Modifier"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </section>
    <?php endfor; ?>
<!--
    TinyMCE other solution : http://toopay.github.io/bootstrap-markdown/
    Multi-Select : http://davidstutz.github.io/bootstrap-multiselect/
    Notifications : http://goodybag.github.io/bootstrap-notify/
    Color-Picker : http://mjolnic.github.io/bootstrap-colorpicker/
    Progressbar : https://github.com/minddust/bootstrap-progressbar
-->
