<?php

namespace Leantime\Domain\Tickets\Controllers {

    use Leantime\Core\Controller;
    use Leantime\Domain\Auth\Models\Roles;
    use Leantime\Domain\Tickets\Services\Tickets as TicketService;
    use Leantime\Domain\Auth\Services\Auth;

    class DelMilestone extends Controller
    {
        private TicketService $ticketService;

        public function init(TicketService $ticketService)
        {
            Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

            $this->ticketService = $ticketService;
        }


        public function get()
        {

            //Only admins
            if (Auth::userIsAtLeast(Roles::$editor)) {
                if (isset($_GET['id'])) {
                    $id = (int)($_GET['id']);
                }

                $this->tpl->assign('ticket', $this->ticketService->getTicket($id));
                $this->tpl->displayPartial('tickets.delMilestone');
            } else {
                $this->tpl->displayPartial('errors.error403');
            }
        }

        public function post($params)
        {

            if (isset($_GET['id'])) {
                $id = (int)($_GET['id']);
            }

            //Only admins
            if (Auth::userIsAtLeast(Roles::$editor)) {
                if (isset($params['del'])) {
                    $result = $this->ticketService->deleteMilestone($id);

                    if ($result === true) {
                        $this->tpl->setNotification($this->language->__("notification.milestone_deleted"), "success");
                        $this->tpl->redirect(BASE_URL . "/tickets/roadmap");
                    } else {
                        $this->tpl->setNotification($this->language->__($result['msg']), "error");
                        $this->tpl->assign('ticket', $this->ticketService->getTicket($id));
                        $this->tpl->displayPartial('tickets.delMilestone');
                    }
                } else {
                    $this->tpl->displayPartial('errors.error403');
                }
            } else {
                $this->tpl->displayPartial('errors.error403');
            }
        }
    }

}
