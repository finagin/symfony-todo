<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends FOSRestController
{
    /**
     * List all tasks.
     *
     * @Route("tasks.json")
     * @Method("GET")
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", nullable=true, description="Offset from which to start listing notes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="100", nullable=true, description="How many notes to return.")
     *
     * @ApiDoc(
     *   section = "Task",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful.",
     *     400 = "Некорректный запрос. Некорректные входные параметры.",
     *     403 = "Доступ запрещен.",
     *   }
     * )
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(ParamFetcherInterface $paramFetcher)
    {
        $manager = $this->getDoctrine()->getManager();

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');

        $tasks = $manager->getRepository(Task::class)
            ->findBy([], null, $limit, $offset);

        return new JsonResponse(['response' => $tasks]);
    }

    /**
     * Store task.
     *
     * @Route("tasks.json")
     * @Method("POST")
     * @Annotations\RequestParam(name="title", description="Название.")
     * @Annotations\RequestParam(name="parent", requirements="(\d+)", strict=false, nullable=false, description="Родительская задача.")
     *
     * @ApiDoc(
     *   section = "Task",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful.",
     *     400 = "Некорректный запрос. Некорректные входные параметры.",
     *     403 = "Доступ запрещен.",
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function storeAction(ParamFetcherInterface $paramFetcher)
    {
        $manager = $this->getDoctrine()
            ->getManager();

        $title = $paramFetcher->get('title');
        $parent = $paramFetcher->get('parent');

        $task = new Task();
        $task->setUser($this->getUser());
        $task->setTitle($title);
        if (!empty($parent)) {
            $parent_id = $parent;
            $parent = $manager->getRepository(Task::class)
                ->find($parent_id);

            if (is_null($parent)) {
                throw new BadRequestHttpException('Parent with id "'.$parent_id.'" not found');
            } elseif (!is_null($parent->getUser()) && $parent->getUser()->getId() !== $this->getUser()->getId()) {
                throw new BadRequestHttpException('Доступ запрещен.', 403);
            }

            $task->setParent($parent);
        }

        $manager->persist($task);
        $manager->flush();

        return new JsonResponse(['response' => ['id' => $task->getId()]]);
    }
}
