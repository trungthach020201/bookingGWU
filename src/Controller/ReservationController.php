<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

date_default_timezone_set('Asia/Ho_Chi_Minh');

class ReservationController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }      
    // Admin

    private function rejectAction(int $id, string $txtReason, ReservationRepository $ResRe):void
    {
        $to = $ResRe->findDetailOfReservation($id)[0]['email'];
        $room = $ResRe->findDetailOfReservation($id)[0]['name'];

        $text = 'Your Request to booking room '.$room.'  has been reject because with reason "' .$txtReason .'"';

        $email = (new Email())
        ->from('gwctbookingroom@gmail.com')
        ->to($to)
        ->subject('Booking Room request reject')
        ->text($text);
        ;

        $this->mailer->send($email);

        $ResRe->deleteReservation($id);
    }

    /**
     * @Route("/reject", name="reject", methods={"POST"})
     */
    public function reject(Request $req, ReservationRepository $ResRe): Response
    { 
        $id = $req->request->get('id');
        $txtReason = $req->request->get('txtReason');

        $this->rejectAction($id,$txtReason,$ResRe);

        return new JsonResponse('Success');
    }

    /**
     * @Route("/checkdata", name="checkdata", methods={"POST"})
     */
    public function checkData(): Response
    {
        return new JsonResponse('Success');
    }

    /**
     * @Route("/accept", name="accept")
     */
    public function accept(Request $req, ReservationRepository $ResRe, UserRepository $useRe): Response
    {
        $id = $req->query->get('id');
        $rejectArrId = $ResRe->acceptReservation($id);

        $to = $ResRe->findDetailOfReservation($id)[0]['email'];
        $room = $ResRe->findDetailOfReservation($id)[0]['name'];
        $roomid = $ResRe->findDetailOfReservation($id)[0]['room_id'];

        foreach ($rejectArrId as $id) {
            $this->rejectAction($id['id'],'This room was booked earlier by someone else. Sorry for the inconvenience.',$ResRe);
        }

        $text = 'Your Request to booking room '.$room.'  has been accept';

        $email = (new Email())
        ->from('gwctbookingroom@gmail.com')
        ->to($to)
        ->subject('Booking Room request accept')
        ->text($text);
        ;

        $this->mailer->send($email);

        return new JsonResponse('Success');
        // return $this->json($rejectArrId);
    }

    /**
     * @Route("/reservation", name="reservation", methods={"POST"})
     */
    public function getReservation(Request $req, ReservationRepository $ResRe): Response
    {
        $arr = $ResRe->findSameOfReservationAndSchedule();

        foreach ($arr as $re) {
            $this->rejectAction($re['id'],'The room you booked is no longer available.',$ResRe);
        }

        // $currentDay = new \DateTime();
        // $formatDay = $currentDay->format('Y-m-d');
        // $ResRe->delExpiredReservation($formatDay);

        $day = $req->request->get('day');
        $from = $req->request->get('from');
        $to = $req->request->get('to');
        $re = $ResRe->findReservation($day, $from, $to);
        return new JsonResponse($re);
    }
    // Admin


    // User
    /**
     * @Route("/form", name="app_form", methods={"POST"})
     */
    public function successAction(Request $req, RoomRepository $repoRoom, ManagerRegistry $reg): Response
    {
        // Call function above
        $req = $this->transformJsonBody($req);

        $user = $this->getUser();
        $room = $req->get('room');
        $objRoom = $repoRoom->findOneBy(['name' => $room]);
        $slot = $req->get('slot');
        $reason = $req->get('reason');
        $day = $req->get('day');
        $dayNew = new \DateTime($day);

        $addReservation = new Reservation();

        $addReservation->setUser($user);
        $addReservation->setRoom($objRoom);
        $addReservation->setReason($reason);
        $addReservation->setDate($dayNew);
        $addReservation->setStatus(0);
        $addReservation->setSlot($slot);

        $entity = $reg->getManager();
        $entity->persist($addReservation);
        $entity->flush();

        $studentMail = $user->getEmail();
        $text = 'Reservation required for room ' . $room . ' from student ' . $studentMail . ' entering the slot ' . $slot . ' with reason: ' . $reason;
        $email = (new Email())
                ->from('gwctbookingroom@gmail.com')
                // Admin mail
                ->to('xx@gmailcom')
                ->subject('New booking request')
                ->text($text);
        $this->mailer->send($email);

        $this->addFlash('success', 'Your reservation request has been sent successfully.');
        return new JsonResponse();
    }

    public function transformJsonBody(Request $re)
    {
        $data = json_decode($re->getContent(), true);
        if ($data === null) {
            return $re;
        }
        $re->request->replace($data);
        return $re;
    }
}
