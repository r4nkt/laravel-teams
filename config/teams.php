<?php

return [

    'models' => [

        'member' => \App\Models\User::class,

        'team' => \R4nkt\Teams\Models\Team::class,

        'membership' => \R4nkt\Teams\Models\Membership::class,

        'invitation' => \R4nkt\Teams\Models\Invitation::class,

    ],

    'actions' => [

        'create_teams' => \R4nkt\Teams\Actions\CreateTeam::class,

        // 'update_teams' => \R4nkt\Teams\Actions\UpdateTeam::class,

        // 'transfer_teams' => \R4nkt\Teams\Actions\TransferTeam::class,

        'delete_teams' => \R4nkt\Teams\Actions\DeleteTeam::class,

        'add_team_members' => \R4nkt\Teams\Actions\AddTeamMember::class,

        // 'update_team_members' => \R4nkt\Teams\Actions\UpdateTeamMember::class,

        'remove_team_members' => \R4nkt\Teams\Actions\RemoveTeamMember::class,

        'invite_team_members' => \R4nkt\Teams\Actions\InviteTeamMember::class,

        'accept_invitations' => \R4nkt\Teams\Actions\AcceptInvitation::class,

        'reject_invitations' => \R4nkt\Teams\Actions\RejectInvitation::class,

        'revoke_invitations' => \R4nkt\Teams\Actions\RevokeInvitation::class,

        'leave_teams' => \R4nkt\Teams\Actions\LeaveTeam::class,

    ],

    'policies' => [

        'invitation' => \R4nkt\Teams\Policies\InvitationPolicy::class,

        'team' => \R4nkt\Teams\Policies\TeamPolicy::class,

    ],

];
