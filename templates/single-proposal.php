<?php

/**
 * The template for displaying the single proposal.
 *
 * This can be overridden by copying it to yourtheme/single-proposal.php.
 *
 * @package ThemePlate
 * @since   0.1.0
 */

use PBWebDev\CardanoPress\Governance\Application;
use PBWebDev\CardanoPress\Governance\Proposal;
use PBWebDev\CardanoPress\Governance\Profile;

$proposalId = get_the_ID();
$userProfile = new Profile(wp_get_current_user());
$proposal = new Proposal($proposalId);
$proposalDates = $proposal->getDates();

$currentStatus = get_post_status();
$statusText = 'Open for Voting';
$dateLabel = 'Closing Date';
$dateText = $proposalDates['end'];

if ('future' === $currentStatus) {
    $statusText = 'Upcoming';
    $dateLabel = 'Starting Date';
    $dateText = $proposalDates['start'];
} elseif ('archive' === $currentStatus) {
    $statusText = 'Complete';
}

get_header();

?>

    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col col-md-10 pt-5">
                <nav class="breadcrumb" style="--bs-breadcrumb-divider: ' ';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href='<?php echo get_post_type_archive_link('proposal'); ?>'>Governance</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                    </ol>
                </nav>

                <h1 class="pb-3"><?php the_title(); ?></h1>
                <p><b>Status: <?php echo $statusText; ?></b>
                <p><b><?php echo $dateLabel; ?>: <?php echo $dateText; ?> UTC</b></p>

                <?php the_content(); ?>

                <p class="pt-3"><a href="#" class="btn btn-primary">Discuss Proposal</a></p>
            </div>
        </div>

        <div class="row justify-content-md-center">
            <div class="col col-md-10 pt-5">
                <div
                    class="row"
                    x-data="cardanoPressGovernance"
                    id="proposal-<?php echo $proposalId; ?>"
                    data-options="<?php echo esc_attr(json_encode($proposal->getData())); ?>"
                    data-voted="<?php echo $userProfile->hasVoted($proposalId); ?>"
                >
                    <div class="col col-md-7">
                        <h2>Vote</h2>
                        <hr/>

                        <?php Application::instance()->template(
                            'proposal/voting-form',
                            compact('proposal')
                        ); ?>
                    </div>

                    <div class="col col-md-5">
                        <?php if ($userProfile->hasVoted($proposal->postId)) : ?>
                            <h2>Vote Stats</h2>
                            <hr/>

                            <?php Application::instance()->template(
                                'proposal/voting-status',
                                compact('proposal')
                            ); ?>
                        <?php else : ?>
                            <h2>Your voting power</h2>
                            <hr/>

                            <template x-if='!isConnected'>
                                <h2>Connect to see voting power</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab adipisci amet animi
                                    corporis, culpa doloribus ducimus eius eos, et fuga hic iure necessitatibus non
                                    nulla
                                    pariatur rem sapiente similique voluptatem.</p>
                            </template>

                            <template x-if='isConnected'>
                                <h2><?php echo $proposal->getVotingPower($userProfile); ?>&curren;</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorum nostrum sunt
                                    voluptas. Assumenda consectetur illo, incidunt labore quia sequi voluptas! Ad
                                    distinctio dolore fugiat iste iusto non officiis. Aut, repellat.</p>
                            </template>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

get_footer();
