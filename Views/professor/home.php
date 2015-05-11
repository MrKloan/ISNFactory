<?php if($this->dispatcher->Core->Config->calendar >= 1): ?>
    <section class="jumbotron background1">
        <div class="container">
            <div class="periods">
                <?php
                    $beggin = ($this->dispatcher->model->date->month > '09') ? $this->dispatcher->model->date->year : $this->dispatcher->model->date->year-1;
                    for($cur_year=$beggin ; $cur_year < $beggin + count($this->dispatcher->model->date->dates) ; $cur_year++):
                ?>
                <?php foreach ($this->dispatcher->model->date->dates[$cur_year] as $m => $days): ?>
                    <div class="month" id="month<?php echo $m; ?>">
                        <table class="calendar">
                            <thead>
                                <tr>
                                    <?php /* Affichage du mois et des flêches de direction */ ?>
                                    <th colspan="2" class="navigation">
                                        <?php
                                            if(($m > 1 && $m < 9 && $m-1 >= 1) || ($m > 9 && $m-1 >= 9))
                                                echo("<a href=\"\" id=\"linkMonth".($m-1)."\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a>");
                                            else if($m == 1)
                                                echo("<a href=\"\" id=\"linkMonth12\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a>");
                                        ?>
                                    </th>
                                    <th colspan="3"><?php echo $this->dispatcher->model->date->months[$m-1].' '.$cur_year; ?></th>
                                    <th colspan="2" class="navigation">
                                        <?php
                                            if(($m < 9 && $m+1 <= 8) || ($m >= 9 && $m+1 <= 12))
                                                echo("<a href=\"\" id=\"linkMonth".($m+1)."\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a>");
                                            else if($m == 12)
                                                echo("<a href=\"\" id=\"linkMonth1\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a>");
                                        ?>
                                    </th>
                                </tr>
                                <tr>
                                    <?php /* Affichage du nom du jour en haut du calendrier */ ?>
                                    <?php foreach ($this->dispatcher->model->date->days as $d): ?>
                                        <th><?php echo substr($d,0,3); ?></th>  
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php $end = end($days); foreach ($days as $d=>$w): ?>
                                        <?php $time = strtotime($this->dispatcher->model->date->year."-$m-$d"); ?>
                                        <?php if ($d == 1 && $w-1 != 0 ): ?>
                                            <td colspan="<?php echo $w-1; ?>" class="padding"></td>
                                        <?php endif; ?>
                                        <td class="entry <?php echo(strtotime($this->dispatcher->model->date->year.'-'.$this->dispatcher->model->date->month.'-'.$this->dispatcher->model->date->day) == $time ? 'today' : ''); ?>">
                                            <?php if(isset($this->dispatcher->model->date->events[$time])) echo "<a id=\"linkEvent".$time."\">"; ?>
                                            
                                            <div class="day"><?php echo $d; ?></div>
                                            
                                            <?php /* Affichage de l'identifier et de la popover */ ?>
                                            <?php if(isset($this->dispatcher->model->date->events[$time])): ?>
                                                <?php foreach($this->dispatcher->model->date->events[$time] as $type => $array): ?>
                                                    <div class="<?php echo('identifier-'.$type); ?>"></div>
                                                        <span data-toggle="popover" data-placement="top" data-content="<?php echo($array['title']); ?>" class="<?php echo('popover'.$time); ?>"></span>
                                                <?php endforeach; ?>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($w == 7): ?>
                                            </tr><tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if ($end != 7): ?>
                                        <td colspan="<?php echo 7-$end; ?>" class="padding"></td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; endfor; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
    
    <section class="jumbotron background3">
        <div class="container">
            <h2>Notifications</h2>
            <div class="panel panel-default scroll">
                
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Information</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $notifications = $this->dispatcher->model->getNotifications();
                            if($notifications)
                                for($i=0; $i<count($notifications); $i++)
                                    echo("<tr><td>".$notifications[$i]->type."</td><td>".$notifications[$i]->information."</td><td><form action=\"\" method=\"POST\"><input type=\"hidden\" name=\"notif_id\" value=\"".base64_encode(SALT.$notifications[$i]->id.PEPPER)."\" /><button type=\"submit\" name=\"submit_del_notif\" class=\"btn btn-link icon-cancel remove-link\"></button></form></td></tr>");
                            else
                                echo("<tr><td colspan=\"3\">Aucune notification</td></tr>");
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </section>

<?php if($this->dispatcher->Core->Config->informations >= 1): ?>
    
    <?php  
        $informations = $this->dispatcher->model->getProfInformations();
        $grades = $this->dispatcher->model->getGrades();
    ?>
    <section class="jumbotron background1">
        <div class="container">
            <h2>Informations</h2>
            <div class="panel panel-default">
                <div class="panel-heading"><button title="add" class="glyphicon glyphicon-plus btn btn-primary" data-toggle="modal" data-target="#ajout_modal">Ajouter</button></div>
                <div class="scroll"> 
                    <table class="table table-striped table-hover">
                        <thead>
                        </thead>
                        <tbody>
                            <?php
                                if(count($informations) == false)
                                    echo("<tr><td>Aucune entrée.</td></tr>");
                                else
                                    for($i=0; $i < count($informations); $i++)
                                        echo("<tr data-toggle=\"modal\" data-target=\"#modif_modal_".sha1($informations[$i]->id)."\"><td class=\"col-md-3 col-sm-3 col-xs-3\">".$informations[$i]->type."  :  ".$informations[$i]->title."</td><td class=\"col-md-9 col-sm-9 col-xs-9 information\">".$informations[$i]->content."</td></tr>");
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="modal fade" id="ajout_modal" tabindex="-1" role="dialog" aria-labelledby="ajout_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="ajout_label">Ajout d'informations</h4>                         
                        </div>
                        <form class="form-horizontal" role="form" action="" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type" class="col-sm-4 control-label">Titre de l'information</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="title_new" placeholder="Titre de l'information" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="col-sm-4 control-label">Type de l'information</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="type_new" id="type" placeholder="Type de l'information" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="classe" class="col-sm-4 control-label">Classe concernée</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="grades_new[]" id="classe" multiple required>
                                            <?php  
                                                for($i=0; $i < count($grades); $i++)
                                                    echo("<option value=\"".$grades[$i]->id."\">".$grades[$i]->grade."</option>");
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <textarea class="form-control" name="information_new" rows="5"></textarea>                           
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="submit_info_create" class="btn btn-primary" value="Enregistrer"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <?php for($i=0 ; $i < count($informations) ; $i++): ?>
            <div class="modal fade" id="modif_modal_<?php echo(sha1($informations[$i]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="modif_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="modif_label">Modification d'information</h4>                              
                        </div>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type" class="col-sm-4 control-label">Titre de l'information</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"  name="titre_edit" value="<?php echo($informations[$i]->title); ?>" placeholder="Titre de l'information">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="col-sm-4 control-label">Type de l'information</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"  name="type_edit" value="<?php echo($informations[$i]->type); ?>" placeholder="Type de l'information" required>
                                    </div>

                                </div>          
                                <div class="form-group">
                                    <label for="classe" class="col-sm-4 control-label">Classe concernée</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="grades_edit[]" multiple required>
                                           <?php
                                                $classes = $this->dispatcher->model->getInformations_for($informations[$i]->id);
                                                for($j=0 ; $j < count($grades) ; $j++)
                                                {
                                                    echo("<option value=\"".$grades[$j]->id."\"");
                                                    for($k=0 ; $k < count($classes) ; $k++)
                                                        if($classes[$k]->grade == $grades[$j]->id)
                                                            echo(' selected');
                                                    echo('>'.$grades[$j]->grade.'</option>');
                                                }  
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <textarea class="form-control" name="information_edit" rows="5" required><?php echo($informations[$i]->content);?></textarea>                            
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="information_id" value="<?php echo(base64_encode(SALT.$informations[$i]->id.PEPPER)); ?>"/>
                                <input type="submit" name="submit_info_edit" class="btn btn-primary" value="Modifier"/>
                                <input type="submit" name="submit_info_del" class="btn btn-danger" value="Supprimer"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endfor; ?>

        </div>
    </section>
<?php endif; ?>