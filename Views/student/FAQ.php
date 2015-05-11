	<section class="jumbotron background1">
		<div class="container titre">
            <h1>FAQ</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container">
            <?php
                $faq = $this->dispatcher->model->getFAQ();
                if(count($faq)==0)
                    echo("<div><h2>Aucune entrée</h2></div>");
                else
                {
                    for($i=0 ; $i < count($faq) ; $i++)
                    {
                        echo("<div><h2>".$faq[$i]->question."</h2>".htmlspecialchars_decode($faq[$i]->answer)."</div>");
                        if($i < count($faq)-1)
                            echo("<hr />");
                    }
                }
            ?>
        </div>
	</section>