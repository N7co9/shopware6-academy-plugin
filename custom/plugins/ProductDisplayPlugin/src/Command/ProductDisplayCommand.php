<?php declare(strict_types=1);

namespace ProductDisplayPlugin\Command;

use ProductDisplayPlugin\ProductDisplayService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'plugin:display-products',
    description: 'Displays products from the Product Display Plugin'
)]
class ProductDisplayCommand extends Command
{
    public function __construct(
        private ProductDisplayService $productDisplayService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fetches products via the ProductDisplayService and prints them to the console.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $products = $this->productDisplayService->action();
            $count = count($products);

            $output->writeln('');
            $output->writeln('<fg=cyan;options=bold>=============================================</>');
            $output->writeln('<fg=cyan;options=bold>🚀  Welcome to the Product Display Plugin  🚀</>');
            $output->writeln('<fg=cyan;options=bold>=============================================</>');
            $output->writeln('');

            if ($count === 0) {
                $output->writeln('<fg=yellow>No products found.</>');
                $output->writeln('<comment>Did you configure everything correctly?</comment>');
                $output->writeln('');
                return Command::SUCCESS;
            }

            $output->writeln(sprintf(
                '<info>Found %d product%s:</info>',
                $count,
                $count > 1 ? 's' : ''
            ));

            foreach ($products as $index => $product) {
                $output->writeln('');
                $output->writeln(sprintf(
                    '<comment>┌─── Product #%d ──────────────────────────</comment>',
                    $index + 1
                ));

                $emojiActive = $product['active'] ? '✅' : '❌';
                $title       = $product['title'] ?: 'N/A';
                $description = $product['description'] ?: '—';
                $price       = (string) $product['Price'];
                $stock       = (string) $product['availableStock'];

                $output->writeln(sprintf(
                    '<comment>│</comment>  <fg=magenta;options=bold>📦 Title:</> %s',
                    $title
                ));

                $output->writeln(sprintf(
                    '<comment>│</comment>  <fg=magenta;options=bold>🗒  Description:</> %s',
                    $description
                ));

                $output->writeln(sprintf(
                    '<comment>│</comment>  <fg=magenta;options=bold>💰 Price:</> %s | <fg=magenta;options=bold>📦 Stock:</> %s',
                    $price,
                    $stock
                ));

                $output->writeln(sprintf(
                    '<comment>│</comment>  <fg=magenta;options=bold>🔓 Active:</> %s',
                    $emojiActive
                ));

                $output->writeln('<comment>└──────────────────────────────────────────</comment>');
            }

            $output->writeln('');
            $output->writeln('<info>🎉 Service executed successfully! 🎉</info>');
            $output->writeln('');

            return Command::SUCCESS;

        } catch (\RuntimeException $exception) {
            // Error block
            $output->writeln('');
            $output->writeln('<error>⚠️ Error</error>');
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            $output->writeln('');
            return Command::FAILURE;
        }
    }
}
