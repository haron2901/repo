<?php

namespace App\Controller;

use App\Dto\User\UserRegisterDto;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    public function __construct(private UserService $userService, private ValidatorInterface $validator) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->toArray();
            $dto = new UserRegisterDto($data);

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userService->register($dto->email, $dto->password);

            return new JsonResponse(['message' => 'user is created successfully'], Response::HTTP_CREATED);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): void
    {
    }
    #[Route('/api/logout', name: 'api_logout', methods: ['GET'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }
    #[Route('/register', name: 'page_register', methods: ['GET'])]
    public function returnPageRegister()
    {
        return $this->render('auth/register.html.twig');
    }
    #[Route('/dashboard', name: 'return_page_tasks', methods: ['GET'])]
    public function returnPageDashboard()
    {
        return $this->render('dashboard.html.twig');
    }//ЭТОГО БЫТЬ ЗДЕСЬ НЕ ДОЛЖНО, ПРОСТО НЕ ЗАХОТЕЛ ОТДЕЛЬНЫЕ КОНТРОЛЛЕРЫ ПОД АПИ И ПОД ВЫВОД ПИСАТЬ
}
