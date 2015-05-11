	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Liens</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container">
            <div class="panel panel-default" id="links">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Adresse</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $links = $this->dispatcher->model->getProfLinks();
                            if(count($links) == false)
                                echo("<tr><td colspan=\"4\">Aucune entrée</td></tr>");
                            else
                                for($i=0; $i < count($links); $i++)
                                    echo("<tr><td>".$links[$i]->title."</td><td>".$links[$i]->description."</td><td><a href=\"".$links[$i]->url."\" target=\"_blank\">".$links[$i]->url."</a></td><td><form action=\"\" method=\"POST\"><a title=\"Modifier\" class=\"icon-pencil update-link\" data-toggle=\"modal\" data-target=\"#update_link_modal_".sha1($links[$i]->id)."\"></a><input type=\"hidden\" name=\"link_id\" value=\"".base64_encode(SALT.$links[$i]->id.PEPPER)."\"/><button type=\"submit\" title=\"Supprimer\" name=\"submit_del_links\" class=\"btn-link icon-cancel remove-link\"></button></form></td>");   
                        ?>
                        <tr>
                            <td colspan="4">
                                <a data-toggle="modal" data-target="#new_link_modal" class="btn btn-primary">Ajouter un lien</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Ajouter un lien -->

                <div class="modal fade" id="new_link_modal" tabindex="-1" role="dialog" aria-labelledby="new_link_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="new_link_label">Ajouter un lien</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="title_create">Nom du lien</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="title_create" id="title_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="description_create">Description</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="description_create" id="description_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="url_create">URL</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="url" name="url_create" id="url_create" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_links_create" class="btn btn-primary" value="Ajouter"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modifier un lien -->
              
                <?php
                    for($i=0; $i < count($links); $i++):
                ?>
                <div class="modal fade" id="update_link_modal_<?php echo(sha1($links[$i]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="update_link_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="update_link_label">Modifier le lien</h4>
                            </div>
                             <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="title_edit">Nom du lien</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="title_edit" id="title_edit" class="form-control" value="<?php echo($links[$i]->title); ?>" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="description_edit">Description</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="description_edit" id="description_edit" class="form-control" value="<?php echo($links[$i]->description); ?>" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="url_edit">URL</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="url_edit" id="url_edit" class="form-control" value=" <?php echo($links[$i]->url); ?> " required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="links_id" value="<?php echo(base64_encode(SALT.$links[$i]->id.PEPPER)); ?>"/>
                                    <input type="submit" name="submit_links_edit" class="btn btn-primary" value="Mettre à jour"/>
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