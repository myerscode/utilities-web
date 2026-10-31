# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [CalVer](https://calver.org/) (`YYYY.MINOR.PATCH`).

## [2026.0.1] - 2026-10-31
### New Features
- [`1625651`](https://github.com/myerscode/utilities-web/commit/1625651e017d549c0a2b2c8745db5ef77646cec8) - **response**: add status helpers and methods *(commit by [@oniice](https://github.com/oniice))*
- [`fcb9845`](https://github.com/myerscode/utilities-web/commit/fcb98455e17641f17a3a5c4cdbfee033b4bc64ed) - **content**: add new content methods *(commit by [@oniice](https://github.com/oniice))*
- [`346f456`](https://github.com/myerscode/utilities-web/commit/346f4568be050938ff041a9d70eda86258ae245f) - **uri**: add URL manipulation methods *(commit by [@oniice](https://github.com/oniice))*
- [`15af4da`](https://github.com/myerscode/utilities-web/commit/15af4da88156c1a283e0e3492cae7a9f8a275099) - **response-util**: add head and isAlive *(commit by [@oniice](https://github.com/oniice))*
- [`f554be5`](https://github.com/myerscode/utilities-web/commit/f554be52cad8b17967f25d03b0204db1f8cb0d5b) - **utility**: add make, response, isAlive *(commit by [@oniice](https://github.com/oniice))*
- [`b97e063`](https://github.com/myerscode/utilities-web/commit/b97e0632707ff37ad6b2a5ee431762506ed1f014) - **dom**: add convenience methods *(commit by [@oniice](https://github.com/oniice))*
- [`cef1627`](https://github.com/myerscode/utilities-web/commit/cef16279a4370a784d5904737aa9c7998ca73af1) - **ci**: add shared dependabot auto-merge *(PR [#9](https://github.com/myerscode/utilities-web/pull/9) by [@oniice](https://github.com/oniice))*

### Bug Fixes
- [`ca6c075`](https://github.com/myerscode/utilities-web/commit/ca6c0757171efd6ff9241df153d23bdda89450f4) - **uri**: anchor scheme detection regex *(commit by [@oniice](https://github.com/oniice))*
- [`8ef18df`](https://github.com/myerscode/utilities-web/commit/8ef18df1b7615221c47e0ad61a096ba64381a75a) - **ping**: fix IPv6 detection and add features *(commit by [@oniice](https://github.com/oniice))*
- [`06f2c0b`](https://github.com/myerscode/utilities-web/commit/06f2c0bed12ecf9babcdeffdbb55ef4783fa08b0) - **content**: preserve HTTP status on 4xx *(commit by [@oniice](https://github.com/oniice))*
- [`345cc07`](https://github.com/myerscode/utilities-web/commit/345cc07548a53fac5364309d4fee2c4715e0ab0a) - **ci**: install required extensions + isolate flaky network ping test *(PR [#12](https://github.com/myerscode/utilities-web/pull/12) by [@oniice](https://github.com/oniice))*

### Refactors
- [`274c5db`](https://github.com/myerscode/utilities-web/commit/274c5db11fd452247991f1318f0244487e5af296) - **exceptions**: add base exception class *(commit by [@oniice](https://github.com/oniice))*


## [2026.0.0] - 2026-04-01
### Bug Fixes
- [`df2cd98`](https://github.com/myerscode/utilities-web/commit/df2cd988e922d2f84d0943ab864dd79e03a1214e) - **ping**: commands where miss configured *(commit by [@oniice](https://github.com/oniice))*
- [`1c53278`](https://github.com/myerscode/utilities-web/commit/1c5327823aba57a14e3f1d5bd1238fa39cd9fea5) - **github**: enable ping on ubuntu acions *(commit by [@oniice](https://github.com/oniice))*
- [`07cd960`](https://github.com/myerscode/utilities-web/commit/07cd960c01fff475e64cdc9c9cb2dc8c8852ee4e) - **github**: get the dependancies installing *(commit by [@oniice](https://github.com/oniice))*
- [`d1c54b9`](https://github.com/myerscode/utilities-web/commit/d1c54b9547dd0d36431fb2404c600c58a58cc98b) - **ping**: regex for finding ping time cross platform *(commit by [@oniice](https://github.com/oniice))*
- [`cb6d868`](https://github.com/myerscode/utilities-web/commit/cb6d868ea3c0fdadd92fda3fae80a1322335024b) - **deps**: added sockets as dependacy *(commit by [@oniice](https://github.com/oniice))*
- [`36b7d77`](https://github.com/myerscode/utilities-web/commit/36b7d7708c89ecec851b1bd06d496806f5c84113) - **ping**: checking ping exists *(commit by [@oniice](https://github.com/oniice))*
- [`a79e76b`](https://github.com/myerscode/utilities-web/commit/a79e76b57142cdbc7706c3dddfd3c2c7df41f4e8) - **tests**: prevent running ping tests on windows *(commit by [@oniice](https://github.com/oniice))*
- [`b1729a2`](https://github.com/myerscode/utilities-web/commit/b1729a26730959d6b4aff75375088aabed9a1d27) - **ci**: add ext-sockets for mock-webserver *(commit by [@oniice](https://github.com/oniice))*

### Refactors
- [`8f304fa`](https://github.com/myerscode/utilities-web/commit/8f304faf0ad15fb04abcabc8c2253d4a19cea7b2) - **ping**: check that ping command exists before running *(commit by [@oniice](https://github.com/oniice))*
- [`5593c80`](https://github.com/myerscode/utilities-web/commit/5593c80eef7a213dd94695ca1d057cea3a9b85ab) - apply Rector modernisation rules *(commit by [@oniice](https://github.com/oniice))*
- [`5473074`](https://github.com/myerscode/utilities-web/commit/5473074c5dac14ed4bf9d3546124d17714e67200) - remove dead code and unused imports *(commit by [@oniice](https://github.com/oniice))*
- [`8ab8f3f`](https://github.com/myerscode/utilities-web/commit/8ab8f3f5629e1936dc3bc2d380b98dc5ac24cf89) - modernise PHP codebase *(commit by [@oniice](https://github.com/oniice))*
- [`7dd71bf`](https://github.com/myerscode/utilities-web/commit/7dd71bfdafa84ca3dc008212453ca4767a31a7a1) - modernise codebase with Rector *(commit by [@oniice](https://github.com/oniice))*
- [`402b456`](https://github.com/myerscode/utilities-web/commit/402b456610eeb4b62157729a3e7aba4944397508) - modernise codebase with Rector *(commit by [@oniice](https://github.com/oniice))*
- [`6b7b293`](https://github.com/myerscode/utilities-web/commit/6b7b293d29dfa60f22774151ae175be1b79cab0e) - remove dead checkUrl from UriUtility *(commit by [@oniice](https://github.com/oniice))*

### Tests
- [`af15f37`](https://github.com/myerscode/utilities-web/commit/af15f374cd2673a05053cf32a5b91b07e6edf725) - improve coverage from 90 to 136 tests *(commit by [@oniice](https://github.com/oniice))*
- [`bdb5466`](https://github.com/myerscode/utilities-web/commit/bdb54664dabe97440437710a4bebc0f7f6499de5) - improve coverage to 92.93% lines *(commit by [@oniice](https://github.com/oniice))*
- [`f6caf3b`](https://github.com/myerscode/utilities-web/commit/f6caf3bf971142e8719a7b0c612a8c9d2c6f48a3) - improve coverage and fix CI extensions *(commit by [@oniice](https://github.com/oniice))*


## [1.3.1](https://github.com/myerscode/utilities-web/releases/tag/1.3.1) - 2019-05-24

- [`6fc57fa`](https://github.com/myerscode/utilities-web/commit/6fc57fad0e74ee7d1d3bd7b11525f2c4cbcddbda) feat: added method to added query params to url
- [`6f7f5ff`](https://github.com/myerscode/utilities-web/commit/6f7f5ff7f1df146ba35123a795a81479130d8e9b) chore: updated code coverage properties
- [`6196f66`](https://github.com/myerscode/utilities-web/commit/6196f660210c77d0bf01f1a08077a2b7251f8b1f) chore: bumped dependancy versions
- [`8e00d3f`](https://github.com/myerscode/utilities-web/commit/8e00d3f6da9e9a906b8f831e21221d365724f023) test: fixed failing test for exception checking
- [`bcab932`](https://github.com/myerscode/utilities-web/commit/bcab932affd465d59d316852a0ed51ee980c263c) chore: removed reference to url utility
[2026.0.0]: https://github.com/myerscode/utilities-web/compare/2025.0.0...2026.0.0
[2026.0.1]: https://github.com/myerscode/utilities-web/compare/2026.0.0...2026.0.1
