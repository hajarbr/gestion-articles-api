<?php
namespace App\Controller;

use App\Document\Article;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractController
{
    #[Route('/api/articles', name: 'create_article', methods: ['POST'])]
    public function createArticle(
        Request $request,
        DocumentManager $dm,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setAuthor($user);
        $article->setPublishedAt(new \DateTime());

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $dm->persist($article);
        $dm->flush();

        return $this->json([
            'message' => 'Article created successfully',
            'article' => [
                'id' => (string) $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'author' => $article->getAuthor()->getName(),
                'publishedAt' => $article->getPublishedAt() ? $article->getPublishedAt()->format('Y-m-d\TH:i:s\Z') : null, // Format the date or return null
            ]
        ]);
    }

    #[Route('/api/articles', name: 'list_articles', methods: ['GET'])]
    public function listArticles(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUser(); // Get the authenticated user
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
    
        $page = max(1, (int) $request->query->get('page', 1)); // Default to page 1
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', 5)); // Default to 5 items per page
    
        $repository = $dm->getRepository(Article::class);
    
        // Get total number of articles for the authenticated user
        $totalItems = $repository->createQueryBuilder()
            ->field('author')->references($user)
            ->count()
            ->getQuery()
            ->execute();
    
        // Calculate offset
        $offset = ($page - 1) * $itemsPerPage;
    
        // Fetch paginated articles for the authenticated user
        $articles = $repository->createQueryBuilder()
            ->field('author')->references($user)
            ->sort('title', 'ASC')
            ->skip($offset)
            ->limit($itemsPerPage)
            ->getQuery()
            ->execute();
    
        // Format the response
        $data = array_map(function (Article $article) {
            return [
                'id' => (string) $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'author' => $article->getAuthor()->getName(), // Get the author's name
                'publishedAt' => $article->getPublishedAt() ? $article->getPublishedAt()->format('Y-m-d\TH:i:s\Z') : null, // Format the date or return null
            ];
        }, iterator_to_array($articles));
    
        return new JsonResponse([
            'data' => $data,
            'totalItems' => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $itemsPerPage),
        ]);
    }

    #[Route('/api/articles/{id}', name: 'update_article', methods: ['PUT'])]
    public function updateArticle(
        string $id,
        Request $request,
        DocumentManager $dm,
        ValidatorInterface $validator
    ): JsonResponse {
        $article = $dm->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], 404);
        }

        $user = $this->getUser();
        if ($article->getAuthor() !== $user) {
            return new JsonResponse(['error' => 'You can only edit your own articles'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $article->setTitle($data['title'] ?? $article->getTitle());
        $article->setContent($data['content'] ?? $article->getContent());

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $dm->flush();

        return new JsonResponse(['message' => 'Article updated successfully'], 200);
    }

    #[Route('/api/articles/{id}', name: 'delete_article', methods: ['DELETE'])]
    public function deleteArticle(string $id, DocumentManager $dm): JsonResponse
    {
        $article = $dm->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], 404);
        }

        $user = $this->getUser();
        if ($article->getAuthor() !== $user) {
            return new JsonResponse(['error' => 'You can only delete your own articles'], 403);
        }

        $dm->remove($article);
        $dm->flush();

        return new JsonResponse(['message' => 'Article deleted successfully'], 200);
    }
}