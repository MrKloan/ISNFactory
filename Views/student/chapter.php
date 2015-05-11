    <?php
        $course = $this->dispatcher->model->getStudentCourse($this->dispatcher->model->param);
        $files = unserialize(base64_decode($course->files_url));
    ?>

    <section class="jumbotron background1">
        <div class="container titre">
            <h1>Chapitre <?php echo($course->chapter); ?> : <?php echo($course->title); ?></h1>
        </div>
    </section>

    <section class="jumbotron background3">
        <div class="container groups">
            <?php 
                if($course->description != NULL)
                    echo("<h2>Description</h2>".htmlspecialchars_decode($course->description));
                if($files):                        
            ?>
            <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                <div class="carousel-inner">
                    <?php for($i=0; $i < count($files); $i++): ?>
                        <?php if(substr($files[$i],strrpos($files[$i], ".")+1,strlen($files[$i])) == "pdf"): ?>
                            <div class="item <?php echo(($i==0) ? "active" : ""); ?>">
                                <div class="carousel-disposition">
                                    <a frameborder="0" class="pdf" href="<?php echo($files[$i]); ?>"></a> 
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <a class="left carousel-control" style="width:8%;" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                <a class="right carousel-control" style="width:8%;" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
            </div>
            <?php endif; ?>
        </div> 
    </section>    

    <section class="jumbotron background1">
        <div class="container">
            <h2>Supports de cours</h2>
            <div class="panel panel-default">
            <table class="table table-striped table-hover">
                <tr>
                    <th>#</th>
                    <th>Document</th>
                    <th>Télécharger</th>
                </tr>
                <?php 
                    if($course->files_url == NULL)
                        echo("<tr><td colspan=\"3\">Aucune entrée.</td></tr>");
                    else
                        for($i=0; $i < count($files); $i++)
                            echo("<tr><td>".($i+1)."</td><td>".substr($files[$i],strrpos($files[$i], "/")+1,strlen($files[$i]))."</td><td><a href=\"".$files[$i]."\"><span class=\"glyphicon glyphicon-save\"></span></a></td></tr>");
                ?>
              </table>
            </div>
        </div>
    </section>