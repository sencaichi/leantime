<?php

namespace Leantime\Domain\Tickets\Controllers {

    use Leantime\Core\Controller;
    use Leantime\Domain\Projects\Services\Projects as ProjectService;
    use Leantime\Domain\Tickets\Services\Tickets as TicketService;
    use Leantime\Domain\Sprints\Services\Sprints as SprintService;
    use Leantime\Domain\Timesheets\Services\Timesheets as TimesheetService;
    class ShowKanban extends Controller
    {
        private ProjectService $projectService;
        private TicketService $ticketService;
        private SprintService $sprintService;
        private TimesheetService $timesheetService;

        public function init(
            ProjectService $projectService,
            TicketService $ticketService,
            SprintService $sprintService,
            TimesheetService $timesheetService
        ) {
            $this->projectService = $projectService;
            $this->ticketService = $ticketService;
            $this->sprintService = $sprintService;
            $this->timesheetService = $timesheetService;

            $_SESSION['lastPage'] = CURRENT_URL;
            $_SESSION['lastTicketView'] = "kanban";
            $_SESSION['lastFilterdTicketKanbanView'] = CURRENT_URL;
        }

        public function get(array $params)
        {

            $template_assignments = $this->ticketService->getTicketTemplateAssignments($params);
            array_map([$this->tpl, 'assign'], array_keys($template_assignments), array_values($template_assignments));

            $this->tpl->assign('allKanbanColumns', $this->ticketService->getKanbanColumns());

            $this->tpl->display('tickets.showKanban');
        }

        public function post(array $params)
        {

            //QuickAdd
            if (isset($_POST['quickadd']) == true) {
                $result = $this->ticketService->quickAddTicket($params);

                if (is_array($result)) {
                    $this->tpl->setNotification($result["message"], $result["status"]);
                }
            }

            $this->tpl->redirect(CURRENT_URL);
        }
    }

}
