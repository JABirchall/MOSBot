MOSBot - The marbles on stream auto-enter bot
=
Status
- [ ] Working
This bot was made possible by Pixel By pixel's lack of authorization enforcement experiance. They have since Corrected their twitch authorization. You are only able to join a single channel in whicht the authorization is for.

# Getting started

Find a stream with the Marbles on stream Twitch Extension. Open dev tools > networking tab and filter requests by domain `pixelbypixelcanada.com`
Once you capture the request copy the streamer ID and your Authorization header for that stream and put them in the `config.json`
Then just run `php start.php`

![https://i.imgur.com/rUA8u03.png](https://i.imgur.com/rUA8u03.png)

