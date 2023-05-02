<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

date_default_timezone_set('Asia/Ho_Chi_Minh');
class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function homeAction(Request $req, RoomRepository $r, UserRepository $repoUsers, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $userId = $this->getUser();
        
        if($userId != null){
            $user = $repoUsers->find($userId);

            if($userPasswordHasher->isPasswordValid($user, "12345678")) {
                return $this->render('user/changepassword.html.twig',[
                    'isDefaultPassword' => true
                ]);   
            }
        }
        

        // data of $day in sort by
        $day = $req->get('day');
        $currentDay = new \DateTime();
        
        if (isset($day)) :
            $schedule = $r->FindRoom($day);
         else :
            $formatDay = $currentDay->format('Y-m-d');
            $day = $formatDay;
            //Get data is the json of Rooms
            $schedule = $r->FindRoom($formatDay);
        endif;

        $arraySchedule = array();
        //Create data in 2d array with key is name of room and subkey is slot
        foreach ($schedule as $entry) {
            $room = $entry['name'];
            $slot = $entry['slot'];
            if (!isset($arraySchedule[$room])) {
                $arraySchedule[$room] = array();
                $arraySchedule[$room]['class'] = $entry['class'];
            }
            $arraySchedule[$room][$slot] = $entry;
        }

            // Sort by day
            $thisWeek = new \DateTime(date('Y-m-d', strtotime("sunday -1 week")));
            $nextFiveWeekDays = array();

            for ($i = 0; $i < 6; $i++) {
                $dayAfter = $thisWeek->modify('tomorrow');
                $date= $dayAfter->format('Y-m-d');
                $name = date("l", strtotime($dayAfter->format('Y-m-d')));
                $nextFiveWeekDays[$date] = $name;
            }

        // return
        return $this->render('main/index.html.twig', [
            'schedule' => $arraySchedule,
            'dayinweek' => $nextFiveWeekDays,
            'currentDay' => $currentDay,
            'days' => $day
        ]);
    }

    /**
     * @Route("/changepassword", name="app_changepassword", methods={"GET|POST"})
     */
    public function ChangPassword(Request $req, UserRepository $repoUsers, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $reg): Response
    {
        // Check this account have default password
        $userId = $this->getUser();
        $user = $repoUsers->find($userId);

        // Form submit
        if (isset($_POST['btnSubmit'])) {
            $passNew = $req->request->get('passwordNew');

            // Check the user changed the password for the rest of the time
            if (isset($_POST['oldPass'])) {
                $oldPass = $req->request->get('oldPass');

                if ($userPasswordHasher->isPasswordValid($user, $oldPass)) {
                    $encodedPassword = $userPasswordHasher->hashPassword($user, $passNew);

                    $user->setPassword($encodedPassword);
                    $entity = $reg->getManager();
                    $entity->persist($user);
                    $entity->flush();

                    $this->addFlash('success', 'Change password successfully');
                } else {
                    $this->addFlash('error', 'Old password does not match');
                }

                return $this->render('user/changepassword.html.twig');
            } else {
                $encodedPassword = $userPasswordHasher->hashPassword($user, $passNew);

                $user->setPassword($encodedPassword);
                $entity = $reg->getManager();
                $entity->persist($user);
                $entity->flush();

                return $this->redirectToRoute('app_home');
            }
        }
        
        if($userPasswordHasher->isPasswordValid($user, "12345678")) {
            return $this->render('user/changepassword.html.twig',[
                'isDefaultPassword' => true
            ]);   
        }

        return $this->render('user/changepassword.html.twig');  
    }
}
