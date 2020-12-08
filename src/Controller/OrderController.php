<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/getOrders")
     * @return array
     */
    public function getOrders(): Response
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get detailed information on a single order.
     * @Route("/getOrder")
     * @return array
     */
    public function getOrder()
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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