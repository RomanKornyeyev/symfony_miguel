<?php

namespace App\Controller;

# BASE IMPORTS
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

# MODELS/ENTITIES, FORMS AND REPOSITORIES
use App\Entity\Nota;
use App\Form\NotaType;
use Doctrine\ORM\EntityManagerInterface;

# SECURITY
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

final class NotasController extends AbstractController
{
    // Listar todas las notas
    #[Route('/notas', name: 'app_nota')]
    public function index(EntityManagerInterface $em): Response
    {
        $notas = $em->getRepository(Nota::class)->findAll();

        return $this->render('notas/index.html.twig', [
            'notas' => $notas,
        ]);
    }

    // Editar o crear una nota
    #[Route('/notas/editar/{id}', name: 'app_nota_edit')]
    public function edit(Request $request, EntityManagerInterface $em, ?int $id = null): Response
    {
        // Si el ID no es nulo, se considera que se está editando una nota existente
        if ($id !== null) {
            $nota = $em->getRepository(Nota::class)->find($id);
    
            if (!$nota) {
                throw $this->createNotFoundException('La nota no existe.');
            }
        } else { // Si el ID es nulo, se considera que se está creando una nueva nota
            $nota = new Nota();
        }
    
        // Crear el formulario
        $form = $this->createForm(NotaType::class, $nota);
        $form->handleRequest($request);
    
        // Si el formulario se envía y es válido, guardar la nota
        /*
        * Esto incluye validación automática de CSRF
        * y validación de los campos según las reglas definidas en el formulario (en este caso por el modelo/entidad Nota)
        */
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($nota); // Persistir la nota (ya sea nueva o editada)
            $em->flush(); // Guardar los cambios en la base de datos
    
            $this->addFlash('success', $id !== null ? 'Nota actualizada correctamente.' : 'Nota creada correctamente.');
    
            return $this->redirectToRoute('app_nota');
        }
    
        return $this->render('notas/editar.html.twig', [
            'form' => $form->createView(),
            'editando' => $id !== null,
        ]);
    }

    // Eliminar una nota
    #[Route('/notas/eliminar', name: 'app_nota_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Recogemos el ID de la nota desde el formulario
        $id = $request->request->get('id');
    
        // Recogemos el token CSRF enviado por el formulario
        $submittedToken = $request->request->get('csrf_token');
    
        // Verificamos si el token es válido (seguridad contra ataques CSRF)
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('borrar_nota', $submittedToken))) {
            // Si el token no es válido, mostramos un mensaje de error y redirigimos
            $this->addFlash('danger', 'Token CSRF inválido.');
            return $this->redirectToRoute('app_nota');
        }
    
        // Buscamos la nota en la base de datos por su ID
        $nota = $em->getRepository(Nota::class)->find($id);
    
        // Si no se encuentra la nota, mostramos un mensaje de error
        if (!$nota) {
            $this->addFlash('danger', 'La nota no existe.');
            return $this->redirectToRoute('app_nota');
        }
    
        // Eliminamos la nota y guardamos los cambios en la base de datos
        $em->remove($nota);
        $em->flush();
    
        // Redirigmos al listado y mostramos un mensaje de éxito al usuario
        $this->addFlash('success', 'Nota eliminada correctamente.');
        return $this->redirectToRoute('app_nota');
    }
    
    
}
