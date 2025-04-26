<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use App\Repository\TareaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TareaRepository::class)]
class Tarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "El título no puede estar vacío.")]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "El título no puede estar vacío.")]
    private ?string $contenido = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    public function __construct()
    {
        $this->fechaCreacion = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getfechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setfechaCreacion(\DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }
}
