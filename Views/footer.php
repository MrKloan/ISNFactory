<?php if($this->dispatcher->action == "index" || !$this->dispatcher->Core->User->getRole()) : ?>
    <footer class="jumbotron background2 footer">
		<div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-1 col-sm-offset-1 col-xs-offset-1">
                <figure>
                    <figcaption>Design :</figcaption>
                    <a target="_blank" href="http://nikolah.net/"><img src="<?php echo(STYLES.'img/nikolah.png'); ?>" alt="NIKOLAH"/></a>
                </figure>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-2">
                <h4>MR Laurent JOSPIN : contact@isnfactory.fr</h4>
        		<p>
        			LYCÉE ALBERT EINSTEIN<br/>
        			Avenue de la Liberté<br/>
        			91700 Sainte Geneviève des Bois<br/>
        			01 69 46 11 11
        		</p>
            </div>

            <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-2">
                <figure>
                    <figcaption>Par des étudiants de : </figcaption>
                    <a target="_blank" href="http://www.esgi.fr/"><img src="<?php echo(STYLES.'img/esgi.png'); ?>" alt="ESGI"/></a>
                </figure>
            </div>
        </div>
	</footer>
<?php else: ?>
	<footer class="jumbotron footer">
		<div class="container">
            <div class="row">
                <?php if(($this->dispatcher->Core->User->getRole() == "role_professor" && $this->dispatcher->Core->Config->faq >= 1) || ($this->dispatcher->Core->User->getRole() == "role_student" && $this->dispatcher->Core->Config->faq == 2)) : ?>
                    <div class="navbar-collapse navbar-header"> 
                        <a href="<?php echo(WEB_ROOT.'/extranet/'.$this->dispatcher->controller.'/FAQ'); ?>" class="btn btn-lg btn-primary">FAQ</a>
                    </div>
                <?php endif; ?>
                <div class="col-xs-6 col-md-2 navbar-right">
                    <a href="<?php echo(WEB_ROOT.'/extranet/'.$this->dispatcher->controller.'/home'); ?>">
                        <img src="<?php echo(STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                    </a>
                </div>
                
            </div>
        </div>
	</footer>
<?php endif; ?>

	<script src="<?php echo(STYLES.'js/jquery-1.11.0.min.js'); ?>"></script>
	<script src="<?php echo(STYLES.'js/bootstrap.min.js'); ?>"></script>

    <script type="text/javascript">
        $('#log-popup a.close').click(function(){
            $('#log-popup').hide();
            return false;
        });
    </script>


<?php if($this->dispatcher->Core->User->getRole()) : ?>
    <?php if($this->dispatcher->view == "home" && $this->dispatcher->Core->User->getRole() == "role_student") : ?>
        <script type="text/javascript">
            $('.trim').hide();
            $('#trim<?php echo(substr($trimester,1,2)); ?>').show();
            $('#trims a').click(function(e){
                var number = $(this).attr('href').substring(2,3);
                $('#trim'+number).show();
                if(number == 1)
                {
                    $('#trim2').hide();
                    $('#trim3').hide();
                }
                else if(number == 2)
                {
                    $('#trim1').hide();
                    $('#trim3').hide();
                }
                else
                {
                    $('#trim1').hide();
                    $('#trim2').hide();
                }
            });
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->view == "home" && $this->dispatcher->Core->User->getRole() != "role_admin") : ?>
    	<script type="text/javascript">
            jQuery(function($){
                var current = '<?php echo($this->dispatcher->model->date->month); ?>'.replace('0', '');    
                $('.month').hide();
                $('#month'+current).show();

                $('.navigation a').click(function(){
                    var month = $(this).attr('id').replace('linkMonth','');
                    if(month != current){
                        $('#month'+current).slideUp();
                        $('#month'+month).slideDown();
                        current = month;
                    }
                    return false; 
                });

                $('.entry a').click(function() {
                    var ev = $(this).attr('id').replace('linkEvent', '');
                    $('.popover'+ev).popover('toggle');
                    return false;
                });
            });
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->view == "project" || $this->dispatcher->view == "project_page" || $this->dispatcher->view == "notes" || $this->dispatcher->view == "final") : ?>
        <?php if(isset($_POST['show_todo']) && $_POST['show_todo'] == '1') : ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    $('#to-do_modal').modal('show');
                });
            </script>
        <?php endif; ?>

        <script type="text/javascript" src="<?php echo(STYLES.'js/jquery-ui-1.10.4.min.js'); ?>"></script>
        <script type="text/javascript">
            $(function(){
                $(".datepicker").datepicker();
            });

            $(document).ready(function(){
                make_draggable($('.note'));
            });

            var zIndex = 0;

            function make_draggable(elements)
            {
                elements.draggable({
                    containment:'parent',
                    start:function(e,ui)
                    {
                        ui.helper.css('z-index',++zIndex);
                    },
                    stop:function(e,ui)
                    {
                        var position = ui.position.left+"x"+ui.position.top+"x"+zIndex;
                        document.getElementById("position_"+ui.helper.find('input.data').attr('value')).value = position;      
                    }
                });
            }
        </script>
    <?php endif; ?>
    <?php if($this->dispatcher->view == "final"): ?>
        <script type="text/javascript" src="<?php echo(STYLES.'js/bootstrap-slider.js'); ?>"></script>
        <script type="text/javascript">
            $('.slider').slider();
            $('.slider').on('slide', function(ev){
                    document.getElementById("slide").value = ev.value;
                });
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->view == "project" || $this->dispatcher->view == "final" || $this->dispatcher->view == "mails" || ($this->dispatcher->Core->User->getRole() == "role_professor" && $this->dispatcher->view == "home" || $this->dispatcher->view == "courses" || $this->dispatcher->view == "FAQ")) : ?>
        <script type="text/javascript" src="<?php echo(STYLES.'js/tinymce/tinymce.min.js'); ?>"></script>
        <script type="text/javascript">
             tinymce.init({
                selector: "textarea",  
            });
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->Core->User->getRole() == "role_professor" && $this->dispatcher->view == "project"): ?>
        <script type="text/javascript">
            <?php for($i=0; $i < count($diff); $i++): ?>
                $('#new_group_div_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>').hide();
                
                $('#new_group_type_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>').change(function() {
                    if($('#new_group_type_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>').val() == 2)
                        $('#new_group_div_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>').show();
                    else
                        $('#new_group_div_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>').hide();
                });
            <?php endfor; ?>
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->view == "chapter") :?>
        <script type="text/javascript" src="<?php echo(STYLES.'js/jquery.media.js'); ?>"></script>
        <script type="text/javascript">
    	    $(function() {
    			$('a.pdf').media({width:1138, height:850}); //Lis aussi bien les pdf mais aussi les vidéos, mp3, ... check jquery.media.js
    	    });
        </script>
    <?php endif; ?>

    <?php if($this->dispatcher->Core->User->getRole() <> "role_student" && $this->dispatcher->view == "home" || $this->dispatcher->view == "users" || $this->dispatcher->view == "courses" ||  $this->dispatcher->view == "project" ||  $this->dispatcher->view == "final"): ?>
    <script src="<?php echo(STYLES.'js/bootstrap-switch.min.js'); ?>"></script>
    <script type="text/javascript">
        $('.switch').bootstrapSwitch('size', 'mini');
        $('.switch').bootstrapSwitch('onText', 'Oui');
        $('.switch').bootstrapSwitch('offText', 'Non');
    </script>
    <?php endif;?>
<?php endif; ?>

<?php if($this->dispatcher->Core->User->getRole() == "role_admin") : ?>
    <script type="text/javascript">
        $('.switch-prof-calendar').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-calendar').bootstrapSwitch('state', false);
                $('.switch-student-calendar').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-calendar').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-homeworks').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-homeworks').bootstrapSwitch('state', false);
                $('.switch-student-homeworks').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-homeworks').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-notes').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-notes').bootstrapSwitch('state', false);
                $('.switch-student-notes').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-notes').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-infos').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-infos').bootstrapSwitch('state', false);
                $('.switch-student-infos').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-infos').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-links').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-links').bootstrapSwitch('state', false);
                $('.switch-student-links').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-links').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-courses').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-courses').bootstrapSwitch('state', false);
                $('.switch-student-courses').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-courses').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-codiad').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-codiad').bootstrapSwitch('state', false);
                $('.switch-student-codiad').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-codiad').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-ftp').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-ftp').bootstrapSwitch('state', false);
                $('.switch-student-ftp').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-ftp').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-projects').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-projects').bootstrapSwitch('state', false);
                $('.switch-student-projects').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-projects').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-conseils').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-conseils').bootstrapSwitch('state', false);
                $('.switch-student-conseils').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-conseils').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-faq').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-faq').bootstrapSwitch('state', false);
                $('.switch-student-faq').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-faq').bootstrapSwitch('disabled', false);
        });
        $('.switch-prof-mails').on('switchChange', function (e, data) {
            if(data.value == false) {
                $('.switch-student-mails').bootstrapSwitch('state', false);
                $('.switch-student-mails').bootstrapSwitch('disabled', true);
            }
            else
                $('.switch-student-mails').bootstrapSwitch('disabled', false);
        });
    </script>
<?php endif; ?>

</body>
</html>