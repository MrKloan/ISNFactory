<?php if($this->dispatcher->Core->Config->calendar == 2): ?>	
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

<?php if($this->dispatcher->Core->Config->informations == 2): ?>
	<?php  $informations = $this->dispatcher->model->getStudentInformations(); ?>
    <section class="jumbotron background3">
		<div class="container">
            <h1>Informations</h1>
            <?php 
                if(count($informations) == 0)
                    echo("<div><h2>Aucune information disponible.</h2></div>");
                else
                {
                    for($i=0; $i < count($informations); $i++)
                    {
                        echo("<div><h2>".$informations[$i]->type." : ".$informations[$i]->title."</h2>".$informations[$i]->content."</div>");
                        if($i < count($informations)-1)
                            echo("<hr />");
                    }
                }
            ?>
        </div>
	</section>
<?php endif; ?>

<?php if($this->dispatcher->Core->Config->notes == 2): ?>
    <?php 
        $works = $this->dispatcher->model->getWorks();
        $trimester = $this->dispatcher->model->getCurrentTrimester();
    ?>
	<section class="jumbotron background1">
		<div class="container">
            <h1>Notes</h1>
            <div class="row">
                <div class="col-xs-9 col-md-9">
                    <div class="panel panel-default">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="trims">
                            <li <?php echo(($trimester == 't1') ? 'class="active"' : ""); ?>><a href="#t1" data-toggle="tab">1er Trimestre</a></li>
                            <li <?php echo(($trimester == 't2') ? 'class="active"' : ""); ?>><a href="#t2" data-toggle="tab">2eme Trimestre</a></li>
                            <li <?php echo(($trimester == 't3') ? 'class="active"' : ""); ?>><a href="#t3" data-toggle="tab">3eme Trimestre</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane <?php echo(($trimester == 't1') ? 'active' : ""); ?>" id="t1">
                                <table class="table table-striped table-hover table-responsive">
                                    <thead>
                                        <tr><th>Type</th><th>Devoirs</th><th>Notes</th><th>Coefficients</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            if($works[0])
                                                for($i=0; $i<count($works[0]); $i++)
                                                    echo("<tr><td>".ucfirst($works[0][$i]->type)."</td><td>".$works[0][$i]->title."</td><td>".$this->dispatcher->model->getNote($works[0][$i]->id)->note."</td><td>".$works[0][$i]->coeff."</td></tr>");
                                            else
                                                echo("<tr><td colspan=\"4\">Aucune note</td></tr>");
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane <?php echo(($trimester == 't2') ? 'active' : ""); ?>" id="t2">
                                <table class="table table-striped table-hover table-responsive">
                                    <thead>
                                        <tr><th>Type</th><th>Devoirs</th><th>Notes</th><th>Coefficients</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            if($works[1])
                                                for($i=0; $i<count($works[1]); $i++)
                                                    echo("<tr><td>".ucfirst($works[1][$i]->type)."</td><td>".$works[1][$i]->title."</td><td>".$this->dispatcher->model->getNote($works[1][$i]->id)->note."</td><td>".$works[1][$i]->coeff."</td></tr>");
                                            else
                                                echo("<tr><td colspan=\"4\">Aucune note</td></tr>");
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane <?php echo(($trimester == 't3') ? 'active' : ""); ?>" id="t3">
                                <table class="table table-striped table-hover table-responsive">
                                    <thead>
                                        <tr><th>Type</th><th>Devoirs</th><th>Notes</th><th>Coefficients</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            if($works[2])
                                                for($i=0; $i<count($works[2]); $i++)
                                                    echo("<tr><td>".ucfirst($works[2][$i]->type)."</td><td>".$works[2][$i]->title."</td><td>".$this->dispatcher->model->getNote($works[2][$i]->id)->note."</td><td>".$works[2][$i]->coeff."</td></tr>");
                                            else
                                                echo("<tr><td colspan=\"4\">Aucune note</td></tr>");
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $moyenne = $this->dispatcher->model->getMoyenne();
                    $moyclasse = $this->dispatcher->model->getMoyPlusMoins();
                ?>
                <?php for($i=0; $i<3; $i++): ?>
                <div class="col-xs-3 col-md-3 trim" id="<?php echo('trim'.($i+1)); ?>">
                    <div class="panel panel-default">
                        <table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr><th>Moyennes</th><th>Notes</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Moyenne de l'élève</td><td><?php echo($moyenne[$i]); ?></td>
                                </tr>
                                <tr>
                                    <td>Moyenne la plus haute</td><td><?php echo(($moyclasse[$i] != false && is_numeric($moyclasse[$i][1])) ? number_format($moyclasse[$i][1],2,',','') : $moyclasse[$i][1]); ?></td>
                                </tr>
                                <tr>
                                    <td>Moyenne la plus basse</td><td><?php echo(($moyclasse[$i] != false && is_numeric($moyclasse[$i][0])) ? number_format($moyclasse[$i][0],2,',','') : $moyclasse[$i][0]); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
	</section>
<?php endif; ?>