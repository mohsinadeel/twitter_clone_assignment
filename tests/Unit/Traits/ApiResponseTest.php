<?php

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

describe('ApiResponse Trait', function () {
    beforeEach(function () {
        // Create a test controller that uses the ApiResponse trait
        $this->controller = new class extends Controller {
            use ApiResponse;

            // Make protected methods public for testing
            public function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
            {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $data,
                ], $code);
            }

            public function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
            {
                $response = [
                    'success' => false,
                    'message' => $message,
                ];

                if ($errors !== null) {
                    $response['errors'] = $errors;
                }

                return response()->json($response, $code);
            }

            public function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
            {
                return $this->errorResponse($message, 422, $errors);
            }

            public function notFoundResponse(string $message = 'Resource not found'): JsonResponse
            {
                return $this->errorResponse($message, 404);
            }
        };
    });

    describe('successResponse', function () {
        it('returns success response with data', function () {
            $data = ['id' => 1, 'name' => 'Test'];
            $response = $this->controller->successResponse($data, 'Success message', 200);

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(200);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => true,
                'message' => 'Success message',
                'data' => $data,
            ]);
        });

        it('returns success response without data', function () {
            $response = $this->controller->successResponse(null, 'Success message');

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(200);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => true,
                'message' => 'Success message',
                'data' => null,
            ]);
        });

        it('uses default message when not provided', function () {
            $response = $this->controller->successResponse(['test' => 'data']);

            $responseData = $response->getData(true);
            expect($responseData['message'])->toBe('Success');
        });

        it('uses default status code when not provided', function () {
            $response = $this->controller->successResponse(['test' => 'data']);

            expect($response->getStatusCode())->toBe(200);
        });
    });

    describe('errorResponse', function () {
        it('returns error response with message', function () {
            $response = $this->controller->errorResponse('Error message', 400);

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(400);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => false,
                'message' => 'Error message',
            ]);
        });

        it('returns error response with errors', function () {
            $errors = ['field' => ['The field is required.']];
            $response = $this->controller->errorResponse('Validation failed', 422, $errors);

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(422);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ]);
        });

        it('uses default message when not provided', function () {
            $response = $this->controller->errorResponse();

            $responseData = $response->getData(true);
            expect($responseData['message'])->toBe('Error');
        });

        it('uses default status code when not provided', function () {
            $response = $this->controller->errorResponse('Error message');

            expect($response->getStatusCode())->toBe(400);
        });
    });

    describe('validationErrorResponse', function () {
        it('returns validation error response', function () {
            $errors = ['email' => ['The email field is required.']];
            $response = $this->controller->validationErrorResponse($errors, 'Validation failed');

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(422);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ]);
        });

        it('uses default message when not provided', function () {
            $errors = ['field' => ['Error']];
            $response = $this->controller->validationErrorResponse($errors);

            $responseData = $response->getData(true);
            expect($responseData['message'])->toBe('Validation failed');
        });
    });

    describe('notFoundResponse', function () {
        it('returns not found response', function () {
            $response = $this->controller->notFoundResponse('Resource not found');

            expect($response)->toBeInstanceOf(JsonResponse::class);
            expect($response->getStatusCode())->toBe(404);

            $responseData = $response->getData(true);
            expect($responseData)->toBe([
                'success' => false,
                'message' => 'Resource not found',
            ]);
        });

        it('uses default message when not provided', function () {
            $response = $this->controller->notFoundResponse();

            $responseData = $response->getData(true);
            expect($responseData['message'])->toBe('Resource not found');
        });
    });
});
