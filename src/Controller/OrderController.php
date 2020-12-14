<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use PDO;

class OrderController extends AbstractController
{

    private $db;

    public function __construct()
    {
        $user = 'root';
        $password = 'root';
        $db = 'original_cottages';
        $host = 'localhost';

        $this->db = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    }

    /**
     * Get a lightly detailed list of all orders
     * @return Response
     */
    public function getOrders()
    {
        //Common table expressions are much nicer than subqueries but unfortunately MAMP's MySQL is too old.
        //It also refuses to accept PIPES_AS_CONCAT so here we are using functions like an animal.
        $stmt = $this->db->prepare("
            SELECT o.id,
                   DATE_FORMAT(date, '%d/%m/%Y') AS date,
                   CONCAT(c.firstname, ' ', c.lastname) AS name,
                   ( 
                       SELECT SUM(i.price) AS total
                       FROM order_details od
                            INNER JOIN inventory i on od.item_id = i.id
                       WHERE order_id = o.id
                   ) AS total
            FROM orders o
                INNER JOIN customer c on o.customer = c.id
            ");

        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $response = new JsonResponse();
        $response->setContent(json_encode($data));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Get detailed information on a single order.
     * @param Request
     * @return Response
     */
    public function getOrder(Request $request)
    {
        $stmt = $this->db->prepare("
            SELECT o.id,
                   date,
                   c.firstname || ' ' || c.lastname AS name,
                   (
                       SELECT SUM(i.price) AS total
                       FROM order_details od
                            INNER JOIN inventory i on od.item_id = i.id
                       WHERE order_id = o.id
                   ) AS total
            FROM orders o
                INNER JOIN customer c on o.customer = c.id
            ");

        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $response = new JsonResponse();
        $response->setContent(json_encode($data));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Add an order.
     * @Route("/getOrder")
     * @return string
     */
    public function addOrder()
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare("
            INSERT INTO
            ");
        //TODO: finish the insert into and handle params.

        try{
            $stmt->execute();
            $this->db->commit();
            return "success";
        } catch(Exception $e){
            $this->db->rollBack();
            return "failure";
        }
    }
}