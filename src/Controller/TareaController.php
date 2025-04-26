<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tarea;
use App\Form\TareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

final class TareaController extends AbstractController
{
    #[Route('/tareas', name: 'app_tarea')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // 1. Crea la consulta base para obtener todas las notas ordenadas por fecha de creación
        $queryBuilder = $em->getRepository(Tarea::class)->createQueryBuilder('n')->orderBy('n.fechaCreacion', 'DESC');

        // 2. Adapta el QueryBuilder de Doctrine para que Pagerfanta pueda paginarlo (usa LIMIT y OFFSET internamente)
        $adapter = new QueryAdapter($queryBuilder);

        // 3. Crea el paginador con la fuente de datos adaptada
        $pagerfanta = new Pagerfanta($adapter);

        // 4. Define cuántos resultados mostrar por página
        $pagerfanta->setMaxPerPage(3);

        // 5. Define cuántos resultados mostrar por página
        $page = $request->query->getInt('page', 1);

        // 6. En caso de que el usuario se salga del rango de paginas, le redirigimos a la primera evitando un error 500
        try {
            $pagerfanta->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException $e) {
            return $this->redirectToRoute('app_tarea', ['page' => 1]); // Redirigir a la primera página si el número de página no es válido
        }

        // 7. Pasamos esos datos al template (vista)
        return $this->render('tarea/index.html.twig', [
            'tareas_vista' => $pagerfanta,
        ]);
    }

    // Editar o crear una nota
    #[Route('/tareas/editar/{id}', name: 'app_tarea_edit')]
    public function edit(Request $request, EntityManagerInterface $em, ?int $id = null): Response
    {
        // 1. Comprobar si estamos editando una tarea existente o estamos creando una nueva
        if ($id !== null) {
            $tarea = $em->getRepository(Tarea::class)->find($id);

            if (!$tarea) {
                throw $this->createNotFoundException('La tarea no existe.');
            }
        }else{
            $tarea = new Tarea ();
        }

        // 2. Crear el formulario vacio o con datos en base a si estamos editando/creando
        $form = $this->createForm(TareaType::class, $tarea);
        $form->handleRequest($request);
        
        // 3. Hacer la comprobacion del formulario (si se ha enviado) y guardar los datos en la base de datos
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tarea); // Persistir la nota (ya sea nueva o editada)
            $em->flush(); // Guardar los cambios en la base de datos

            $this->addFlash('success', $id !== null ? 'Nota actualizada correctamente.' : 'Nota creada correctamente.');

            return $this->redirectToRoute('app_tarea');
        }

        // Renderizar la vista
        return $this->render('tarea/editar.html.twig', [
            'form' => $form->createView(),
            'editando' => $id !== null,
        ]);
    }

    // Eliminar una tarea
    #[Route('/tareas/eliminar', name: 'app_tarea_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // 1. Recoger los datos del formulario (id de la tarea borrar, token csrf)
        $id = $request->request->get('id');
        $submittedToken = $request->request->get('csrf_token');

        // 2. Comprobamos que el token csrf sea válido
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('borrar_tarea', $submittedToken))) {
            $this->addFlash('danger', 'Token CSRF inválido.');
            return $this->redirectToRoute('app_tarea');
        }

        // 3. Si el token csrf es correcto, buscamos la tarea en la base de datos por su id
        $tarea = $em->getRepository(Tarea::class)->find($id);
        if (!$tarea) {
            $this->addFlash('danger', 'La tarea no existe.');
            return $this->redirectToRoute('app_tarea');
        }

        // 4. Eliminamos la tarea
        $em->remove($tarea);
        $em->flush();

        // 5. Redireccionamos con un mensaje de éxito
        $this->addFlash('success', 'Tarea eliminada correctamente.');
        return $this->redirectToRoute('app_tarea');
    }
}


