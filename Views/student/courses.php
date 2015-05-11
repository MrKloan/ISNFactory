    <section class="jumbotron background1">
        <div class="container titre">
            <h1>Cours</h1>
        </div>
    </section>

    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="panel panel-default">
                <table class="table table-hover">
                    <tbody>
                        <?php
                            $courses = $this->dispatcher->model->getCourses();
                            if(count($courses)==0)
                                echo("<tr><td>Aucun cours disponible.</td></tr>");
                            else
                                for($i=0; $i < count($courses); $i++)
                                    echo("<tr><td><a href=\"".WEB_ROOT."/extranet/student/chapter/".$courses[$i]->chapter."\">Chapitre ".$courses[$i]->chapter." : ".$courses[$i]->title."</a></td></tr>");
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>