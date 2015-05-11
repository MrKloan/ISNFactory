	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Base de données</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container" id="sgbd">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading head">Log Screen</div>
                        <div class="log">
                            <?php if(isset($_POST['sgbd_log'])): print_r($_POST['sgbd_log']); else: ?>
                                <strong><em>Attention :</em></strong> cette page permettant d'interagir directement avec la base de données du site, son accès doit être <strong>étroitement contrôlé</strong>.
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sx-12 col-sm-12 col-md-4">
                    <div class="panel panel-default custom-panel">
                        <div class="panel-heading head">Requête personnalisée</div>
                        <form action="" method="POST">
                            <textarea name="custom_request" class="form-control"></textarea>
                            <input type="submit" name="submit_sgbd_custom" value="Envoyer la requête" class="btn btn-primary"/>
                        </form>
                    </div>
                </div>

                <div class="col-sx-12 col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading head">Requêtes formatées</div>
                        <form action="" method="POST">
                        	<div class="input-group">
                       			<span class="input-group-addon">SELECT</span>
                       			<input type="text" name="sgbd_select_0" class="form-control"/>
                        		<span class="input-group-addon">FROM</span>
                       			<input type="text" name="sgbd_select_1" class="form-control"/>
                        		<span class="input-group-addon">WHERE</span>
                       			<input type="text" name="sgbd_select_2" class="form-control"/>
                                <span class="input-group-addon">;</span>
                       			
                       			<span class="input-group-btn">
              				        <button type="submit" name="submit_sgbd_select" class="btn btn-primary">Envoyer la requête</button>
              				    </span>
                       		</div>

                        	<div class="input-group">
                        		<span class="input-group-addon">UPDATE</span>
                       			<input type="text" name="sgbd_update_0" class="form-control"/>
                        		<span class="input-group-addon">SET</span>
                       			<input type="text" name="sgbd_update_1" class="form-control"/>
                        		<span class="input-group-addon">WHERE</span>
                       			<input type="text" name="sgbd_update_2" class="form-control"/>
                                <span class="input-group-addon">;</span>
                       			
                       			<span class="input-group-btn">
              				        <button type="submit" name="submit_sgbd_update" class="btn btn-primary">Envoyer la requête</button>
              				    </span>
                       		</div>

                       		<div class="input-group">
                       			<span class="input-group-addon">INSERT INTO</span>
                       			<input type="text" name="sgbd_insert_0" class="form-control"/>
                        		<span class="input-group-addon">VALUES (</span>
                       			<input type="text" name="sgbd_insert_1" class="form-control"/>
                                <span class="input-group-addon">);</span>
                       			
                       			<span class="input-group-btn">
            				        <button type="submit" name="submit_sgbd_insert" class="btn btn-primary">Envoyer la requête</button>
            				    </span>
                       		</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
	</section>