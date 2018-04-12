<?php
namespace BabysitterBundle\Controller;

use AppBundle\Entity\Babysitter;
use AppBundle\Form\Model\StartConversationModel;
use AppBundle\Form\Type\StartConversationType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BabysitterMessageController extends Controller
{
    const MESSAGES_PER_PAGE = 5;

    public function indexAction($name)
    {

    }
    public function inboxAction()
    {
        // ...
        $entityManager=$this->get('doctrine.orm.entity_manager');
        $driver = new \FOS\Message\Driver\Doctrine\ORM\DoctrineORMDriver($entityManager);
        $repository = new \FOS\Message\Repository($driver);
        $conversations = $repository->getPersonConversations($this->getUser());

        return $this->render('inbox.html.twig', [ 'conversations' => $conversations ]);
    }
    public function conversationAction()
    {
        // ...
        $entityManager=$this->get('doctrine.orm.entity_manager');
        $driver = new \FOS\Message\Driver\Doctrine\ORM\DoctrineORMDriver($entityManager);
        $repository = new \FOS\Message\Repository($driver);
        $conversations = $this->get('fos_message.repository')->getPersonConversations($this->getUser());
        return $this->render('@Babysitter/Messages/Babysitter_conversation.html.twig', [ 'conversations' => $conversations]);
    }
    public function conversationMessagesAction($id,$page,Request $request)
    {
        // ...
        $entityManager=$this->get('doctrine.orm.entity_manager');
        $driver = new \FOS\Message\Driver\Doctrine\ORM\DoctrineORMDriver($entityManager);
        $repository = new \FOS\Message\Repository($driver);
        $conversation = $repository->getConversation($id);

        // Check access
        if (! $conversation->isPersonInConversation($this->getUser())) {
            throw new AccessDeniedHttpException();
        }

        $total = $conversation->getMessages()->count();
        $offset = ($page - 1) * self::MESSAGES_PER_PAGE;

        if ($offset >= $total) {
            throw $this->createNotFoundException();
        }

        $totalPages = ceil($total / self::MESSAGES_PER_PAGE);
        $messages = $repository->getMessages($conversation, $offset, self::MESSAGES_PER_PAGE);
        foreach ($messages as $message) {
            $messagePerson = $message->getMessagePerson($this->getUser());

            if (! $messagePerson->isRead()) {
                $messagePerson->setRead();
                $this->getDoctrine()->getManager()->persist($messagePerson);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        if ($request->getMethod() == 'POST') {
            $message = $this->get('fos_message.sender')->sendMessage(
                $conversation,
                $this->getUser(),
                $request->get('body')
            );
            $lastPage = ceil(($total + 1) / self::MESSAGES_PER_PAGE);
            $url = $this->generateUrl('Babysitter_conversation_view', [
                'id' => $conversation->getId(),
                'page' => $lastPage
            ]);

            $url .= '#message-' . $message->getId();

            return $this->redirect($url);
        }
        $conversations = $this->get('fos_message.repository')->getPersonConversations($this->getUser());
        return $this->render('@Babysitter/Messages/Babysitter_conversation_view.html.twig', [
            'conversation' => $conversation,
            'conversations' => $conversations,
            'messages' => $messages,
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page
        ]);
    }
}
