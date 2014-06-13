<?
User::logout();
Page::success('Logged Out');
Page::saveMessages();
Http::redirect('/');
?>  
