<?php
class HomeModel extends Model
{
	public $date = NULL;

	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);

		if($dispatcher->controller != "admin")
			$this->date = new Date($this->dispatcher);
	}

    protected function handlePost()
    {
        if(isset($_POST['submit_info_create']))
        {
            $title = isset($_POST['title_new']) ? $_POST['title_new'] : NULL;
            $information = isset($_POST['information_new']) ? $_POST['information_new'] : NULL;
            $grades = isset($_POST['grades_new']) ? $_POST['grades_new'] : NULL;
            $type = isset($_POST['type_new']) ? $this->dispatcher->Core->SQL->secure($_POST['type_new']) : NULL;
            
            if($title !=NULL && $information!=NULL && $grades!=NULL && $type!=NULL)
            {   
                $this->dispatcher->Core->SQL->insert(array('table' => 'informations', 'values' => array(NULL, $title, $type, $information)));
                $info = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'informations', 'conditions' => 'title='.$title));

                $values = array(array());
                for($i=0; $i < count($grades); $i++)
                {
                    $values[$i][0] = $info->id;
                    $values[$i][1] = $grades[$i];
                }

                $this->dispatcher->Core->SQL->insert(array('table' => 'informations_for', 'values' => $values));

                $_POST['success_log'] = 'Votre information a bien été enregistrée.';
            }
            else
                $_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
        }
        else if(isset($_POST['submit_info_del']))
        {
            $id = isset($_POST['information_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['information_id'])))) : NULL;
            
            if($id!=NULL){
                $query = $this->dispatcher->Core->SQL->delete(array('table' => 'informations', 'conditions' => 'id='.$id));
                $_POST['success_log'] = 'Votre information a bien été supprimée.';
            }
            else
                $_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
        }
        else if(isset($_POST['submit_info_edit']))
        {
            $id = isset($_POST['information_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['information_id'])))) : NULL;
            $titre = isset($_POST['titre_edit']) ? $_POST['titre_edit'] : NULL;
            $info =  isset($_POST['information_edit']) ? $_POST['information_edit'] : NULL;
            $grades = isset($_POST['grades_edit']) ? $_POST['grades_edit'] : NULL;
            $type = isset($_POST['type_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['type_edit']) : NULL;
            
            if($id!=NULL && $titre!=NULL && $info!=NULL && $grades!=NULL && $type!=NULL)
            {
                $this->dispatcher->Core->SQL->update(array('table' => 'informations', 'columns' => array('title', 'type', 'content'), 'values' => array($titre, $type, $info), 'conditions' => array('id' => $id)));
                
                $infos = $this->dispatcher->Core->SQL->select(array('table' => 'informations_for', 'conditions' => array('information' => $id)));
                for($i=0 ; $i < count($infos) ; $i++)
                    $this->dispatcher->Core->SQL->delete(array('table' => 'informations_for', 'conditions' => array('information' => $infos[$i]->information, 'grade' => $infos[$i]->grade)));
                for($i=0 ; $i < count($grades) ; $i++)
                    $this->dispatcher->Core->SQL->insert(array('table' => 'informations_for', 'values' => array($id, $grades[$i])));
                
                $_POST['success_log'] = 'Votre information a bien été modifiée.';
            }
            else
                $_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
        }
        else if(isset($_POST['submit_admin_config']))
        {
            $maintenance = isset($_POST['maintenance']) ? '1' : '0';
            $allow_register = isset($_POST['allow_register']) ? '1' : '0';
            $shield_attempts = isset($_POST['shield_attempts']) ? $this->dispatcher->Core->SQL->secure($_POST['shield_attempts']) : '50';
            $shield_duracy = isset($_POST['shield_duracy']) ? $this->dispatcher->Core->SQL->secure($_POST['shield_duracy']) : '25';

            $student_calendar = isset($_POST['student-calendar']) ? 1 : 0;
            $prof_calendar = isset($_POST['prof-calendar']) ? 1 : 0;
            $calendar = (string)($student_calendar + $prof_calendar);

            $student_homeworks = isset($_POST['student-homeworks']) ? 1 : 0;
            $prof_homeworks = isset($_POST['prof-homeworks']) ? 1 : 0;
            $homeworks = (string)($student_homeworks + $prof_homeworks);

            $student_notes = isset($_POST['student-notes']) ? 1 : 0;
            $prof_notes = isset($_POST['prof-notes']) ? 1 : 0;
            $notes = (string)($student_notes + $prof_notes);

            $student_infos = isset($_POST['student-infos']) ? 1 : 0;
            $prof_infos = isset($_POST['prof-infos']) ? 1 : 0;
            $infos = (string)($student_infos + $prof_infos);

            $student_links = isset($_POST['student-links']) ? 1 : 0;
            $prof_links = isset($_POST['prof-links']) ? 1 : 0;
            $links = (string)($student_links + $prof_links);

            $student_courses = isset($_POST['student-courses']) ? 1 : 0;
            $prof_courses = isset($_POST['prof-courses']) ? 1 : 0;
            $courses = (string)($student_courses + $prof_courses);

            $student_codiad = isset($_POST['student-codiad']) ? 1 : 0;
            $prof_codiad = isset($_POST['prof-codiad']) ? 1 : 0;
            $codiad = (string)($student_codiad + $prof_codiad);

            $student_ftp = isset($_POST['student-ftp']) ? 1 : 0;
            $prof_ftp = isset($_POST['prof-ftp']) ? 1 : 0;
            $ftp = (string)($student_ftp + $prof_ftp);

            $student_projects = isset($_POST['student-projects']) ? 1 : 0;
            $prof_projects = isset($_POST['prof-projects']) ? 1 : 0;
            $projects = (string)($student_projects + $prof_projects);

            $student_faq = isset($_POST['student-faq']) ? 1 : 0;
            $prof_faq = isset($_POST['prof-faq']) ? 1 : 0;
            $faq = (string)($student_faq + $prof_faq);

            $student_mails = isset($_POST['student-mails']) ? 1 : 0;
            $prof_mails = isset($_POST['prof-mails']) ? 1 : 0;
            $mails = (string)($student_mails + $prof_mails);

            //Mise à jour
            $this->dispatcher->Core->SQL->update(array('table' => 'config', 'columns' => array('maintenance', 'allow_register', 'shield_attempts', 'shield_duracy', 'calendar', 'homeworks', 'notes', 'informations', 'links', 'courses', 'codiad', 'ftp', 'projets', 'faq', 'mails'), 'values' => array($maintenance, $allow_register, $shield_attempts, $shield_duracy, $calendar, $homeworks, $notes, $infos, $links, $courses, $codiad, $ftp, $projects, $faq, $mails), 'conditions' => array('id', USE_CONFIG)));
            $this->dispatcher->Core->Config = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'config', 'conditions' => 'id='.USE_CONFIG));
        }
        else if(isset($_POST['submit_del_all']))
        {
            $users = $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => 'role<>role_admin'));
            
            for($i=0; $i<count($users); $i++)
                $this->dispatcher->Core->Codiad->deleteAccount($users[$i]->email);

            if(!$this->dispatcher->Core->SQL->delete(array('table' => 'users', 'conditions' => 'role<>role_admin')))
            {
                $_POST['error_log'] = 'Erreur lors de la suppression des utilisateurs et des tables associées par des clés étrangères.';
                return false;
            }
            
            if(!$this->dispatcher->Core->SQL->delete(array('table' => 'notifications')))
            {
                $_POST['error_log'] = 'Erreur lors de la suppression des notifications.';
                return false;
            }
            
            $request = "DELETE FROM login_security WHERE account <> (SELECT email FROM users WHERE role='role_admin');";
            if(!$this->dispatcher->Core->SQL->query($request))
            {
                $_POST['error_log'] = 'Erreur lors de la suppression des login_security.';
                return false;
            }

            $this->clearDirectory(FILES.'Projects/');
            $this->clearDirectory(FILES.'Courses/');

            $this->dispatcher->Core->Codiad->saveJSON('projects.php', array());
        }
        else if(isset($_POST['submit_del_notif']))
        {
            $id = isset($_POST['notif_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['notif_id'])))) : NULL;
            if($id!=NULL)
                $this->dispatcher->Core->SQL->delete(array('table' => 'notifications', 'conditions' => 'id='.$id));
        }
        
        parent::handlePost();
    }

    public function getGrades()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id))); 
    }

    public function getStudentInformations()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        
        $request = "SELECT * FROM informations WHERE id IN (SELECT information FROM informations_for WHERE grade=".$user->grade.");";
        return $this->dispatcher->Core->SQL->query($request);
    }

    public function getProfInformations()
    {
        $grades = $this->getGrades();
        if($grades != false)
        {
            $conditions = "WHERE ";
            for($i=0; $i < count($grades); $i++){
                $conditions .= "grade=".$grades[$i]->id;
                if($i < count($grades)-1)
                   $conditions .= " OR ";
            }

            $request = "SELECT * FROM informations WHERE id IN(SELECT information FROM informations_for ".$conditions.");";
            return $this->dispatcher->Core->SQL->query($request);
        }
    }

    public function getInformations_for($i)
    {
        return $this->dispatcher->Core->SQL->select(array('table' => 'informations_for', 'conditions' => 'information='.$i));
    }

    public function getNotifications()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $request = "SELECT * FROM notifications WHERE grade IN(SELECT id FROM grades WHERE teacher=".$user->id.") ORDER BY id DESC;";
        return $this->dispatcher->Core->SQL->query($request);
    }

    public function getWorks()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => 'id='.$user->grade));
        $trimesters = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => 'teacher='.$grade->teacher));

        $works = array(array());
        if($trimesters != false)
        {
            for($i=0; $i<3; $i++)
            {
                $trim = 'trimester'.($i+1);
                if($i!=2)
                {
                    $trim2 = 'trimester'.($i+2);
                    $request = "SELECT * FROM works WHERE date_end >= '".$trimesters->$trim."' AND date_end < '".$trimesters->$trim2."' AND id IN(SELECT work FROM notes WHERE student=".$user->id.");";
                }
                else
                    $request = "SELECT * FROM works WHERE date_end >= '".$trimesters->$trim."' AND id IN(SELECT work FROM notes WHERE student=".$user->id.");";

                $works[$i] = $this->dispatcher->Core->SQL->query($request);
            }
            return $works;
        }
    }

    public function getCurrentTrimester()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => 'id='.$user->grade));
        $trimesters = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => 'teacher='.$grade->teacher));

        if($trimesters != false)
        {
            if(date("Y-m-d", time()) > $trimesters->trimester1)
            {
                if(date("Y-m-d", time()) > $trimesters->trimester2)
                {
                    if(date("Y-m-d", time()) > $trimesters->trimester3)
                        return 't3';
                    else
                        return 't2';
                }
                else
                    return 't1';
            }
        }
        return 't1';
    }

    public function getNote($work)
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'notes', 'conditions' => array('student' => $user->id, 'work' => $work)));
    }

    public function getMoyenne()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => 'id='.$user->grade));
        $trimesters = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => 'teacher='.$grade->teacher));

        $moy = array();
        if($trimesters != false)
        {
            for($i=0; $i<3; $i++)
            {
                $trim = 'trimester'.($i+1);

                if($i!=2)
                {
                    $trim2 = 'trimester'.($i+2);
                    $request = "SELECT notes.note,works.coeff FROM notes,works WHERE notes.student=".$user->id." AND works.id=notes.work AND works.date_end >= '".$trimesters->$trim."' AND works.date_end < '".$trimesters->$trim2."';";
                }
                else
                    $request = "SELECT notes.note,works.coeff FROM notes,works WHERE notes.student=".$user->id." AND works.id=notes.work AND works.date_end >= '".$trimesters->$trim."';";

                $notes = $this->dispatcher->Core->SQL->query($request);

                $somme = 0;
                $coefficients = 0;
                if($notes)
                {
                    for($j=0; $j<count($notes); $j++)
                    {
                        $somme += $notes[$j]->note * $notes[$j]->coeff;
                        $coefficients += $notes[$j]->coeff;
                    }
                    $moy[$i] = $somme / $coefficients;
                }
                else
                    $moy[$i] = 'Aucune note';
            }
            return $moy;
        }
        return array('Aucune note','Aucune note','Aucune note');
    }

    public function getMoyPlusMoins()
    {
        $user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $students = $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => 'grade='.$user->grade));
        $grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => 'id='.$user->grade));
        $trimesters = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => 'teacher='.$grade->teacher));

        $moins_plus = array(array(20,0),array(20,0),array(20,0));

        if($students && $trimesters)
        {
            for($i=0; $i<3; $i++)
            {
                $trim = 'trimester'.($i+1);
                $flag = false;

                for($j=0; $j<count($students); $j++)
                {  
                    if($i!=2)
                    {
                        $trim2 = 'trimester'.($i+2);
                        $request = "SELECT notes.note,works.coeff FROM notes,works WHERE notes.student=".$students[$j]->id." AND works.id=notes.work AND works.date_end >= '".$trimesters->$trim."' AND works.date_end < '".$trimesters->$trim2."';";
                    }
                    else
                        $request = "SELECT notes.note,works.coeff FROM notes,works WHERE notes.student=".$students[$j]->id." AND works.id=notes.work AND works.date_end >= '".$trimesters->$trim."';";
                    
                    $notes = $this->dispatcher->Core->SQL->query($request);

                    $somme = 0;
                    $coefficients = 0;

                    if($notes)
                    {
                        $flag = true;
                        for($k=0; $k<count($notes); $k++)
                        {
                            $somme += $notes[$k]->note * $notes[$k]->coeff;
                            $coefficients += $notes[$k]->coeff;
                        }
                        $moyenne = $somme/$coefficients;
                        if($moyenne < $moins_plus[$i][0])
                            $moins_plus[$i][0] = $moyenne;
                        if($moyenne > $moins_plus[$i][1])
                            $moins_plus[$i][1] = $moyenne;
                    }
                    else
                    {
                        if(!$flag)
                        {
                            $moins_plus[$i][0] = "Aucune note";
                            $moins_plus[$i][1] = "Aucune note";
                        }
                        continue;
                    }
                    
                }
            }
            return $moins_plus;
        }
        else
            return array(array('Aucune note','Aucune note'),array('Aucune note','Aucune note'),array('Aucune note','Aucune note'));
    }

    /**
     * Suppression récursive d'un répertoire et de son contenu.
     * @return boolean
     */
    private function recursiveRmdir($dir)
    {
        if(is_dir($dir))
        {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach($files as $file)
                is_dir($dir.'/'.$file) ? $this->recursiveRmdir($dir.'/'.$file) : unlink($dir.'/'.$file);
            
            return rmdir($dir);
        }
        else
            return false;
    }

    private function clearDirectory($dir)
    {
        if(false !== ($ressource = opendir($dir)))
        {
            while (false !== ($file = readdir($ressource)))
            {
                if($file != "." && $file != ".." && $file != ".htaccess")
                {
                    if(is_dir($dir."/".$file))
                        $this->recursiveRmdir($dir."/".$file);
                    else
                        unlink($dir."/".$file);
                }
            }
            closedir($ressource);
        }
    }
}

class Date
{
    private $dispatcher = NULL;
	private $sql = NULL;
	public $year = NULL;
	public $month = NULL;
    public $day = NULL;

    public $days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    public $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

    public $events = NULL;
    public $dates = NULL;

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    	$this->sql = $dispatcher->Core->SQL;

    	$this->year = date('Y');
    	$this->month = date('m');
        $this->day = date('d');

    	$this->events = $this->getEvents($dispatcher->controller);
    	$this->dates = $this->getAll();
    }

    public function getEvents($controller)
    {
        $events = array();

        if($controller == "professor")
        {
            $homeworks = $this->sql->select(array('table' => 'works', 'conditions' => "type <> 'projet' AND YEAR(date_end) = ".$this->year.""));
            $projects = $this->sql->select(array('table' => 'projects', 'conditions' => 'YEAR(date_end)='.$this->year));
            $todo = array();//$this->sql->select(array('table' => 'todo_lists', 'conditions' => 'YEAR(deadline)='.$this->year));
            
        }
        else if($controller == "student")
        {
            try 
            {
                $homeworks = $this->sql->base->prepare("SELECT id, date_end, title, type FROM works WHERE type <> 'projet' AND YEAR(date_end) = ".$this->year." AND id IN(SELECT work FROM works_for WHERE grade = ".$this->dispatcher->Core->User->getGrade().");");
                $homeworks->execute();
                $homeworks = $homeworks->fetchAll(PDO::FETCH_OBJ);
            }
            catch(PDOException $e)
            {
                if(DEBUG)
                    die($e);
                else
                    $homeworks = array();
            }
            try 
            {
                $projects = $this->sql->base->prepare("SELECT * FROM projects WHERE id IN(SELECT project FROM projects_for WHERE enabled='1') AND YEAR(date_end) = ".$this->year." AND title IN(SELECT title FROM works WHERE type='projet' AND id IN(SELECT work FROM works_for WHERE grade = ".$this->dispatcher->Core->User->getGrade()."));");
                $projects->execute();
                $projects = $projects->fetchAll(PDO::FETCH_OBJ);

            }
            catch(PDOException $e)
            {
                if(DEBUG)
                    die($e);
                else
                    $projects = array();
            }          

            try
            {
                $todo = $this->sql->base->prepare("SELECT `group`, deadline, title, description FROM todo_lists WHERE YEAR(deadline) = ".$this->year." AND `group` IN(SELECT `group` FROM groups_members WHERE user = (SELECT id FROM users WHERE email = ?));");
                $todo->execute(array($this->dispatcher->Core->User->getEmail()));
                $todo = $todo->fetchAll(PDO::FETCH_OBJ);
            }
            catch(PDOException $e)
            {   
                if(DEBUG)
                    die($e);
                else
                    $todo = array();
            }
        }

        //Homeworks
        for($i=0 ; $i < count($homeworks) ; $i++)
        {
            if(!isset($events[strtotime($homeworks[$i]->date_end)]['homeworks']))
                $events[strtotime($homeworks[$i]->date_end)]['homeworks'] = array('title' => $homeworks[$i]->title);
            else
                $events[strtotime($homeworks[$i]->date_end)]['homeworks']['title'] .= ' + '.$homeworks[$i]->title;
        }
        //Projects
        for($i=0 ; $i < count($projects) ; $i++)
        {
            if(!isset($events[strtotime($projects[$i]->date_end)]['projects']))
                $events[strtotime($projects[$i]->date_end)]['projects'] = array('title' => $projects[$i]->title);
            else
                $events[strtotime($projects[$i]->date_end)]['projects']['title'] .= ' + '.$projects[$i]->title;
        } 
        //To-Do
        for($i=0 ; $i < count($todo) ; $i++)
        {
            if(!isset($events[strtotime($todo[$i]->deadline)]['todo']))
                $events[strtotime($todo[$i]->deadline)]['todo'] = array('title' => $todo[$i]->title, 'content' => $todo[$i]->description);
            else
            {
                $events[strtotime($todo[$i]->deadline)]['todo']['title'] .= ' + '.$todo[$i]->title;
                $events[strtotime($todo[$i]->deadline)]['todo']['content'] .= ' + '.$todo[$i]->description;
            }
        }

        return $events;
    }

    public function getAll()
    {
        $loop = true;
        $dates = array();
        if($this->month >= '09')
            $date = new DateTime($this->year.'-09-01');
        else
            $date = new DateTime(($this->year-1).'-09-01');

        while($loop)
        {
            $y = $date->format('Y');
            $m = $date->format('n');
            $d = $date->format('j');
            $w = str_replace('0','7',$date->format('w'));
            $dates[$y][$m][$d] = $w;

            //Si on a atteint le 31 août, on arrête la boucle
            if($date->format('n') == '08' && $date->format('d') == '31')
                $loop = false;

            $date->add(new DateInterval('P1D'));
        }
        return $dates;
    }
}
?>