	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Liens</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container">
            <div class="panel panel-default">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Adresse</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $links = $this->dispatcher->model->getLinks();
                        if(count($links) == 0)
                            echo("<tr><td colspan=\"3\">Aucun lien disponible.</td><tr>");
                        else
                            for($i=0; $i < count($links); $i++)
                                echo("<tr><td>".$links[$i]->title."</td><td>".$links[$i]->description."</td><td><a href=\"".$links[$i]->url."\" target=\"_blank\">".$links[$i]->url."</a></td></tr>");
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
	</section>