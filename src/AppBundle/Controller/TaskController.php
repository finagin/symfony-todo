<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use FOS\RestBundle\Controller\Annotations\RequestParam;
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
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", nullable=true, description="Offset from which to start listing notes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="100", nullable=true, description="How many notes to return.")
     *
     * @Annotations\View()
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $start = null == $offset ? 0 : $offset + 1;
        $limit = $paramFetcher->get('limit');

        return new JsonResponse(array('data' => 'Success!'));
    }

    /**
     * Store task.
     *
     * @Route("tasks.json")
     * @Method("POST")
     * @RequestParam(name="title", description="Название.")
     * @RequestParam(name="parent", requirements="(\d+)", strict=false, nullable=false, description="Родительская задача.")
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
            }

            $task->setParent($parent);
        }

        $manager->persist($task);
        $manager->flush();

        return new JsonResponse(['response' => ['id' => $task->getId()]]);
    }

    /**
     * Update task.
     *
     * @Route("tasks/{id}.json")
     * @Method("PUT")
     *
     * @ApiDoc(
     *   section = "Task",
     *   resource = true,
     *   parameters = {
     *     {"name"="title", "dataType"="string", "required"=true, "description"="Название."},
     *     {"name"="parent", "dataType"="integer", "required"=false, "description"="Родительская задача."}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful.",
     *     400 = "Некорректный запрос. Некорректные входные параметры.",
     *     403 = "Доступ запрещен.",
     *     404 = "Задание не найдено.",
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param int                   $id
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction($id, ParamFetcherInterface $paramFetcher)
    {
        return new JsonResponse(array('data' => 'Success!'));
    }

    /**
     * Get a single task.
     *
     * @Route("tasks/{id}.json")
     * @Method("GET")
     *
     * @ApiDoc(
     *   section = "Task",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful.",
     *     400 = "Некорректный запрос. Некорректные входные параметры.",
     *     403 = "Доступ запрещен.",
     *     404 = "Задание не найдено.",
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function showAction($id)
    {
        return new JsonResponse(array('data' => 'Success!'));
    }

    /**
     * Delete task.
     *
     * @Route("tasks/{id}.json")
     * @Method("DELETE")
     *
     * @ApiDoc(
     *   section = "Task",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful.",
     *     400 = "Некорректный запрос. Некорректные входные параметры.",
     *     403 = "Доступ запрещен.",
     *     404 = "Задание не найдено.",
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function destroyAction($id)
    {
        return new JsonResponse(array('data' => 'Success!'));
    }
}
