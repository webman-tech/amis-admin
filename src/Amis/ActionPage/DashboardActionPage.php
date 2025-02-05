<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

use WebmanTech\AmisAdmin\Amis\Page;

class DashboardActionPage extends BaseActionPage
{
    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        return Page::make()
            ->withBody(50, [
                'Welcome'
            ]);
    }
}