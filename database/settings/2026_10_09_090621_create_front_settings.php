<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('front.menu_label', 'Menu');
        $this->migrator->add('front.cta_label', 'Add Truckit to Claude');
        $this->migrator->add('front.cta_href', '#setup');
        $this->migrator->add('front.nav', [
            ['label' => 'How it works', 'href' => '#how-it-works'],
            ['label' => 'Try asking', 'href' => '#try-asking'],
            ['label' => 'Questions', 'href' => '#questions'],
        ]);
        $this->migrator->add('front.badge_label', 'New');
        $this->migrator->add('front.badge_text', 'Truckit in Claude');
        $this->migrator->add('front.hero_title', "Moving\nsomething big?\nJust ask Claude.");
        $this->migrator->add('front.hero_body', 'Get an instant Truckit price for your move, right in the chat. Then book it on Truckit in a few taps.');
        $this->migrator->add('front.secondary_cta_label', 'See how it works');
        $this->migrator->add('front.secondary_cta_href', '#how-it-works');
        $this->migrator->add('front.hero_footnote', 'Free to get a price. No sign in needed.');
        $this->migrator->add('front.chat_title', 'Claude');
        $this->migrator->add('front.chat_badge', 'Truckit Connect');
        $this->migrator->add('front.chat_user_message', 'How much to move a 3 seater couch and a fridge from New Farm to Byron Bay next Saturday?');
        $this->migrator->add('front.chat_status', 'Checked Truckit for a price');
        $this->migrator->add('front.chat_reply_before', 'Truckit can do that for');
        $this->migrator->add('front.chat_price', '$396.67 incl. GST');
        $this->migrator->add('front.chat_reply_after', ', picked up and delivered next Saturday. That covers loading, transport and unloading by a rated Truckit provider.');
        $this->migrator->add('front.chat_book_label', 'Book this on Truckit');
        $this->migrator->add('front.chat_domain', 'truckit.net');
        $this->migrator->add('front.how_label', 'How it works');
        $this->migrator->add('front.how_heading', 'Three steps, one question');
        $this->migrator->add('front.steps', [
            ['index' => '01', 'title' => 'Add Truckit to Claude', 'body' => 'It takes under a minute.'],
            ['index' => '02', 'title' => 'Ask for a price', 'body' => '"How much to move a 3 seater couch from New Farm to Byron Bay?"'],
            ['index' => '03', 'title' => 'Book on Truckit', 'body' => 'Tap the link and your job is ready to book.'],
        ]);
        $this->migrator->add('front.try_label', 'Try asking');
        $this->migrator->add('front.try_heading', 'Copy a question, paste it into Claude');
        $this->migrator->add('front.copy_label', 'Copy');
        $this->migrator->add('front.copied_label', 'Copied');
        $this->migrator->add('front.prompts', [
            ['text' => 'How much to move a 2 bedroom apartment from Newtown to Wollongong on 21 December?'],
            ['text' => 'What would it cost to ship 2 pallets from Sydney to Melbourne next week?'],
            ['text' => 'Price to transport my car from Perth to Brisbane'],
        ]);
        $this->migrator->add('front.why_label', 'Why Truckit');
        $this->migrator->add('front.why_heading', 'A real price, not a guess');
        $this->migrator->add('front.why_points', [
            ['title' => 'Priced on the spot', 'body' => 'Truckit prices your job straight away. No forms, no waiting for quotes.'],
            ['title' => 'Rated providers', 'body' => 'Every job goes to a reviewed transport provider.'],
            ['title' => 'Pay safely on Truckit', 'body' => 'You book and pay on Truckit. Never inside the chat.'],
            ['title' => 'Moves of every size', 'body' => 'Furniture, vehicles, pallets and more.'],
        ]);
        $this->migrator->add('front.setup_label', 'Set up');
        $this->migrator->add('front.setup_heading', 'Add Truckit to Claude in under a minute');
        $this->migrator->add('front.setup_body', "Once Truckit is listed in Claude's connector directory, you'll find it there too.");
        $this->migrator->add('front.connector_url', 'https://mcp.truckit.net/mcp');
        $this->migrator->add('front.setup_steps', [
            ['number' => '1', 'text' => 'In Claude, open Settings, then Connectors.', 'show_url' => false],
            ['number' => '2', 'text' => 'Choose Add custom connector.', 'show_url' => false],
            ['number' => '3', 'text' => 'Name it Truckit and paste this address.', 'show_url' => true],
            ['number' => '4', 'text' => 'Start a chat and ask for a price.', 'show_url' => false],
        ]);
        $this->migrator->add('front.questions_label', 'Questions');
        $this->migrator->add('front.questions_heading', 'Good to know');
        $this->migrator->add('front.questions_empty', 'No questions published yet.');
        $this->migrator->add('front.cta_heading', 'Your next move is one question away.');
        $this->migrator->add('front.footer_brand', 'Truckit');
        $this->migrator->add('front.footer_tagline', "Australia's freight marketplace");
        $this->migrator->add('front.footer_disclaimer', 'Claude is a product of Anthropic. Truckit is not affiliated with Anthropic.');
    }
};
