MOSBot - The marbles on stream auto-enter bot
=
Status
- Working
- Unable to join races (After some fun with a bug discovered flooring races)

# Getting started

Find a stream with the Marbles on stream Twitch Extension. Open dev tools > networking tab and filter requests by domain `pixelbypixelcanada.com`
Once you capture the request copy the streamer ID and your Authorization header for that stream and put them in the `config.json`
Then just run `php start.php`

