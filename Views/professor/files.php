<?php 
	$courantdir = FILES;
	$url = $this->dispatcher->model->getParams();
	if(!empty($url))
		$courantdir = FILES.$url."/";
?>
	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Fichiers</h1>
        </div>
	</section>

	<section class="jumbotron background3 groups">
		<div class="container">
			<ol class="breadcrumb fil">
				<li><a href="<?php echo(WEB_ROOT.'/extranet/professor/files/'); ?>"><img class="images" alt="racine" src="<?php echo(STYLES."img/gestionnaire/repertoire.png"); ?>" />Racine</a></li>
				<?php
					$fildariane = array();
					foreach($this->dispatcher->model->getBreadcrumb(str_replace(FILES, "", $courantdir)) as $element)
					{
						$fildariane[] = $element;
						echo("<li><a href=\"".WEB_ROOT."/extranet/professor/files/".implode("/", $fildariane)."/\">".$element."</a></li>");
					}
				?>
			</ol>
			<div class="row">
				<div class="col-md-12">

					<div class="panel panel-default col-md-3 arborescence">
						<?php
							$dirs = $this->dispatcher->model->getDirectories();
						?>
						<ul>
							<li><a href="<?php echo(WEB_ROOT.'/extranet/professor/files/'); ?>"><img class="images" alt="racine" src="<?php echo(STYLES."img/gestionnaire/repertoire.png"); ?>" />Racine</a></li>
							<?php
								foreach($dirs as $element)
									echo("<li class=\"sousdossier\"><a href=\"".WEB_ROOT."/extranet/professor/files/".$element['nom']."/\" ".(FILES.$element['nom']."/" == $courantdir ? "style=\"font-weight:bold;\"" : "")."><img class=\"images\" alt=\"racine\" src=\"".STYLES."img/gestionnaire/repertoire.png\"/>".$element['nom']."</a></li>");
							?>
						</ul>
					</div>

					<?php 
						$contenu = $this->dispatcher->model->getContents($courantdir);
					?>
					<div class="panel panel-default col-md-9">
						<table class="table table-hover">
							<thead>
								<tr>
									<th class="col-md-5 col-xs-5">Nom</th>
									<th class="col-md-3 col-xs-3">Type</th>
									<th class="col-md-2 col-xs-2">Taille</th>
									<th class="col-md-2 col-xs-2">Modifié le</th>							
								</tr>
							</thead>
							<tbody>
								<?php
									$recognized = $this->dispatcher->model->recognized_ext;
									$extensions = $this->dispatcher->model->extensions;

									if(isset($contenu) && !empty($contenu)):
										foreach($contenu as $element):
								?>
								<?php switch($element['type']) {
								
									case 'repertoire': ?>
										<tr>
											<td class="filename"><a href="<?php echo(WEB_ROOT.'/extranet/professor/files/'.str_replace(FILES,"", $courantdir).$element['nom']."/"); ?>"><img class="images" alt="repertoire" src="<?php echo(STYLES."img/gestionnaire/repertoire.png"); ?>" /><?php echo($element['nom']); ?></a></td>
											<td><span>Dossier de fichiers</span></td>
											<td></td>
											<td></td>
										</tr>
									<?php break; ?>
									
									<?php case 'fichier': ?>
										<?php if(in_array(strtolower($element['extension']), $recognized)) : ?>
											<tr>
												<td class="filename"><a href="<?php echo(str_replace(FILES,WEB_ROOT."/Files/", $courantdir).$element['nom.extension']); ?>"><img class="images" alt="repertoire" src="<?php echo(STYLES."img/gestionnaire/".strtolower($element['extension']).".png"); ?>" /><?php echo($element['nom.extension']); ?></a></td>
												<td><span><?php echo $extensions[strtolower($element['extension'])]?></span></td>
												<td><?php echo($this->dispatcher->model->formatSize($element['taille'])); ?></td>
												<td><?php echo date('d/m/Y', $element['date']) ?></td>
											</tr>
										<?php else : ?>
											<tr>
												<td class="filename"><a href="<?php echo(str_replace(FILES,WEB_ROOT."/Files/", $courantdir).$element['nom.extension']); ?>"><img class="images" alt="repertoire" src="<?php echo(STYLES."img/gestionnaire/inconnu.png"); ?>" /><?php echo($element['nom.extension']); ?></a></td>
												<td></td>
												<td><?php echo($this->dispatcher->model->formatSize($element['taille'])); ?></td>
												<td><?php echo date('d/m/Y', $element['date']) ?></td>
											</tr>
										<?php endif; ?>

									<?php break; ?>

								<?php } ?>
								<?php endforeach;?>

								<?php else : ?>
								<tr>
									<td colspan="4"><img class="images" alt="" src="<?php echo(STYLES."img/gestionnaire/info.png"); ?>" />Répertoire vide</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>


	</section>