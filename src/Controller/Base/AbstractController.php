<?php declare(strict_types=1);

namespace App\Controller\Base;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Component\User\CurrentUser;
use App\Controller\Base\Constants\ResponseFormat;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private CurrentUser $currentUser;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CurrentUser $currentUser
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->currentUser = $currentUser;
    }

    /**
     * @return ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @param object $data
     * @param array  $context
     * @throws ValidationException
     */
    protected function validate(object $data, array $context = []): void
    {
        $this->getValidator()->validate($data, $context);
    }

    /**
     * @return SerializerInterface
     */
    protected function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param mixed $content
     * @param int   $status
     * @return Response
     */
    protected function response($content, int $status = Response::HTTP_OK): Response
    {
        return (new Response(
            $this->getSerializer()->serialize($content, ResponseFormat::JSONLD), $status
        ));
    }

    /**
     * @param int $status
     * @return Response
     */
    protected function responseEmpty(int $status = Response::HTTP_OK): Response
    {
        return $this->response('{}', $status);
    }

    /**
     * @param mixed  $content
     * @param int    $status
     * @param string $format
     * @return Response
     */
    protected function responseNormalized(
        $content,
        int $status = Response::HTTP_OK,
        string $format = ResponseFormat::JSONLD
    ): Response {
        $result = $this->getSerializer()->normalize($content, $format);
        return $this->response($result, $status);
    }

    /**
     * @param Request $request
     * @param string  $dtoClass
     * @param string  $format
     * @return object
     */
    protected function getDtoFromRequest(
        Request $request,
        string $dtoClass,
        string $format = ResponseFormat::JSONLD
    ): object {
        return $this->getSerializer()->deserialize(
            $request->getContent(),
            $dtoClass,
            $format
        );
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return $this->currentUser->get();
    }
}