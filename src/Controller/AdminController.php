<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

date_default_timezone_set('Asia/Ho_Chi_Minh');

class AdminController extends AbstractController
{
    /**
     * @Route("/data", name="data", methods={"POST"})
     */
    public function getData(Request $req, ScheduleRepository $ScheRe): Response
    {
        $day = $req->request->get('day');
        $from = $req->request->get('from');
        $to = $req->request->get('to');

        $re = $ScheRe->findScheduleAndReservation($day,$from,$to);

        return new JsonResponse($re);
    }

    /**
     * @Route("/scraperdata", name="scraperdata", methods={"POST"})
     */
    public function scraperdata(): Response
    {
        $command = 'node ../public/js/scraperData/index.js';
        $output = array();
        exec($command, $output);
        return new JsonResponse($output);
    }

    /**
     * @Route("/insertdata", name="insertdata")
     */
    public function insertdata(Request $req): Response
    {
        return $this->json('');
        $data = $req->request->get('data');
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $req, RoomRepository $r): Response
    {
        $curDay = $req->get('day');

        if (!(isset($curDay))) :
            $currentDay = new \DateTime();
            $curDay = $currentDay->format('Y-m-d');
        endif;
    
        $rooms = $r->findAll();
        // Sort by day
        $thisWeek = new \DateTime(date('Y-m-d', strtotime("sunday -1 week")));
        $nextFiveWeekDays = array();

        for ($i = 0; $i < 6; $i++) {
            $dayAfter = $thisWeek->modify('tomorrow');
            $day = $dayAfter->format('Y-m-d');
            $name = date("l", strtotime($dayAfter->format('Y-m-d')));
            $nextFiveWeekDays[$day] = $name;
        }

        return $this->render('admin/index.html.twig', [
            'dayinweek' => $nextFiveWeekDays,
            'rooms' => $rooms,
            'curDay'=>$curDay
        ]);
    }
    //
}
