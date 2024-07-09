<x-app>
  <div class="bg-white">
    <header x-data="{ open: false }" class="absolute inset-x-0 top-0 z-50">
      <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
          <a href="#" class="-m-1.5 p-1.5">
            <span class="sr-only">{{ config('app.name') }}</span>
            <img class="h-8 w-auto" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
          </a>
        </div>
        <div class="flex lg:hidden">
          <button x-on:click="open = true" type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
            <span class="sr-only">Open main menu</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
        </div>
        <div class="hidden lg:flex lg:gap-x-12">
          <a href="#home" class="text-sm font-semibold leading-6 text-gray-900">Home</a>
          <a href="#about" class="text-sm font-semibold leading-6 text-gray-900">About</a>
          <a href="#features" class="text-sm font-semibold leading-6 text-gray-900">Features</a>
          <a href="#pricing" class="text-sm font-semibold leading-6 text-gray-900">Pricing</a>
          <a href="#faq" class="text-sm font-semibold leading-6 text-gray-900">FAQ</a>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
          <a href="{{ route('sign-in') }}" class="text-sm font-semibold leading-6 text-gray-900">Sign in <span aria-hidden="true">&rarr;</span></a>
        </div>
      </nav>
      <!-- Mobile menu, show/hide based on menu open state. -->
      <div x-show="open" class="lg:hidden" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-50"></div>
        <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
          <div class="flex items-center justify-between">
            <a href="#" class="-m-1.5 p-1.5">
              <span class="sr-only">{{ config('app.name') }}</span>
              <img class="h-8 w-auto" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
            </a>
            <button x-on:click="open = false" type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
              <span class="sr-only">Close menu</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-gray-500/10">
              <div class="space-y-2 py-6">
                <a href="#home" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Home</a>
                <a href="#about" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">About</a>
                <a href="#features" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Features</a>
                <a href="#pricing" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Pricing</a>
                <a href="#faq" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">FAQ</a>
              </div>
              <div class="py-6">
                <a href="{{ route('sign-in') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Sign in</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

  <div id="home" class="relative isolate px-6 pt-14 lg:px-8">
      <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-amber-400 to-cyan-400 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
      </div>
      <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
        <div class="hidden sm:mb-8 sm:flex sm:justify-center">
          <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
            Announcing our interactive online quiz. <a href="{{ route('sign-in') }}" class="font-semibold text-primary-600"><span class="absolute inset-0" aria-hidden="true"></span>Try it <span aria-hidden="true">&rarr;</span></a>
          </div>
        </div>
        <div class="text-center">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Study and assess yourself online 🔥</h1>
          <p class="mt-6 text-lg leading-8 text-gray-600">Study and move at your own pace without any pressure. Easily assess yourself to enhance your understanding. Learning made easier and flexible.</p>
          <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="{{ route('sign-up') }}" class="rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Get started</a>
            <a href="#about" class="text-sm font-semibold leading-6 text-gray-900">Learn more <span aria-hidden="true">→</span></a>
          </div>
        </div>
      </div>
      <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-amber-400 to-cyan-400 opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
      </div>
    </div>
  </div>




  <section id="about" class="bg-white">
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="max-w-screen-lg text-gray-500 sm:text-lg">
            <h2 class="mb-4 text-4xl tracking-tight font-bold text-gray-900">Learning and Assessment Application Platform (<span class="font-extrabold">LAAPP</span>)</h2>
            <p class="mb-4 font-light">This program is a Learning and Assessment Application Platform (LAAPP) designed for pupils (learners) from pre-school to senior high school (technical & vocational). The goal of LAAPP is to give learners an opportunity to have good and affordable reading materials on all subjects of their study and to provide a medium for easy assessments of learners’ performances through quizzes, written tests, assignments and examinations. Available now is the assessment aspect of the program for Primary and Junior High Schools learners. Coverage for Senior High School learners and reading materials for all categories of schools to follow soon.</p>
            <p class="mb-4 font-medium">LAAPP is designed and developed by Mon and Associates Technology Ltd, a registered company in Ghana.</p>
            <a href="#features" class="inline-flex items-center font-medium text-primary-600 hover:text-primary-800">
                Learn more
                <svg class="ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            </a>
        </div>
    </div>
  </section>





  <div id="features" class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl lg:text-center">
        <h2 class="text-base font-semibold leading-7 text-primary-600">Learn smarter</h2>
        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Everything you need to learn online</p>
        <p class="mt-6 text-lg leading-8 text-gray-600">We have got all the features that will help you learn virtually. Have more confidence in your hardwork by quizzing yourself.</p>
      </div>
      <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
        <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
          <div class="relative pl-16">
            <dt class="text-base font-semibold leading-7 text-gray-900">
              <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
              </div>
              Simplicity
            </dt>
            <dd class="mt-2 text-base leading-7 text-gray-600">Easy to maneuver interface with high speed loading to meet your demand within a short time.</dd>
          </div>
          <div class="relative pl-16">
            <dt class="text-base font-semibold leading-7 text-gray-900">
              <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
              </div>
              Security
            </dt>
            <dd class="mt-2 text-base leading-7 text-gray-600">It shields your personal information from intruders.</dd>
          </div>
          <div class="relative pl-16">
            <dt class="text-base font-semibold leading-7 text-gray-900">
              <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                </svg>
              </div>
              Flexibility
            </dt>
            <dd class="mt-2 text-base leading-7 text-gray-600">It gives you accessibility on an operating system (iOS, Android and Windows) and any smart phone or tablet.</dd>
          </div>
          <div class="relative pl-16">
            <dt class="text-base font-semibold leading-7 text-gray-900">
              <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
              </div>
              Updates
            </dt>
            <dd class="mt-2 text-base leading-7 text-gray-600">Regular updates of content to keep pace with changing educational curricula, scientific innovations or customers demand.</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>



<section id="pricing"
  class="relative z-20 overflow-hidden bg-white pt-20 pb-12 lg:pt-[120px] lg:pb-[90px]"
>
  <div class="container mx-auto">
    <div class="-mx-4 flex flex-wrap">
      <div class="w-full px-4">
        <div class="mx-auto mb-[60px] max-w-[510px] text-center lg:mb-20">
          <span class="text-primary-600 mb-2 block text-lg font-semibold">
            Affordable Pricing
          </span>
          <h2
            class="text-gray-900 mb-4 text-3xl font-bold sm:text-4xl md:text-[40px]"
          >
            Our Pricing Plan
          </h2>
          <p class="text-gray-600 text-base">
            Simple no-trick pricing with great discount for your money. The more months you pay, the more discount applied.
          </p>
        </div>
      </div>
    </div>
    <div class="-mx-4 flex flex-wrap justify-center">
      <div class="w-full px-4 md:w-1/2 lg:w-1/3">
        <div
          class="border-primary-600 shadow-lg relative z-10 mb-10 overflow-hidden rounded-xl border border-opacity-20 bg-white py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12"
        >
          <span class="text-primary-600 mb-4 block text-lg font-semibold">
            Basic
          </span>
          <h2 class="text-gray-900 mb-5 text-[42px] font-bold">
            GHS 15
            <span class="text-gray-600 text-base font-medium"> / subject </span>
          </h2>
          <p
            class="text-gray-600 mb-8 border-b border-[#F2F2F2] pb-8 text-base"
          >
            Perfect for prolific learners and anyone needing just a short time.
          </p>
          <div class="mb-7">
            <p class="text-gray-600 mb-1 text-base leading-loose">
              All features
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              Free updates
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              3 Months access
            </p>
          </div>
          <a
            href="{{ route('sign-in') }}"
            class="text-primary-600 hover:bg-primary-600 hover:border-primary-600 block w-full rounded-md border border-[#D4DEFF] bg-transparent p-4 text-center text-base font-semibold transition hover:text-white"
          >
            Choose Basic
          </a>
          <div>
            <span class="absolute right-0 top-7 z-[-1]">
              <svg
                width="77"
                height="172"
                viewBox="0 0 77 172"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle cx="86" cy="86" r="86" fill="url(#paint0_linear)" />
                <defs>
                  <linearGradient
                    id="paint0_linear"
                    x1="86"
                    y1="0"
                    x2="86"
                    y2="172"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#0D9488" stop-opacity="0.09" />
                    <stop offset="1" stop-color="#C4C4C4" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </span>
            <span class="absolute right-4 top-4 z-[-1]">
              <svg
                width="41"
                height="89"
                viewBox="0 0 41 89"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle
                  cx="38.9138"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 38.9138 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 38.9138 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 38.9138 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 38.9138 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 38.9138 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 38.9138 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 38.9138 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="1.42021"
                  r="1.42021"
                  transform="rotate(180 38.9138 1.42021)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 26.4157 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 26.4157 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 26.4157 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 26.4157 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 26.4157 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 26.4157 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 26.4157 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 26.4157 1.4202)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 13.9177 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 13.9177 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 13.9177 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 13.9177 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 13.9177 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 13.9177 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 13.9177 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="1.42019"
                  r="1.42021"
                  transform="rotate(180 13.9177 1.42019)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 1.41963 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 1.41963 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 1.41963 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 1.41963 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 1.41963 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 1.41963 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 1.41963 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 1.41963 1.4202)"
                  fill="#0D9488"
                />
              </svg>
            </span>
          </div>
        </div>
      </div>
      <div class="w-full px-4 md:w-1/2 lg:w-1/3">
        <div
          class="border-primary-600 shadow-lg relative z-10 mb-10 overflow-hidden rounded-xl border border-opacity-20 bg-white py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12"
        >
          <span class="text-primary-600 mb-4 block text-lg font-semibold">
            Standard
          </span>
          <h2 class="text-gray-900 mb-5 text-[42px] font-bold">
            GHS 20
            <span class="text-gray-600 text-base font-medium"> / subject </span>
          </h2>
          <p
            class="text-gray-600 mb-8 border-b border-[#F2F2F2] pb-8 text-base"
          >
            Perfect for everyone and anyone needing just a little extra time.
          </p>
          <div class="mb-7">
            <p class="text-gray-600 mb-1 text-base leading-loose">
              All features
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              Free updates
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              6 Months access
            </p>
          </div>
          <a
            href="{{ route('sign-in') }}"
            class="bg-primary-600 border-primary-600 block w-full rounded-md border p-4 text-center text-base font-semibold text-white transition hover:bg-opacity-90"
          >
            Choose Standard
          </a>
          <div>
            <span class="absolute right-0 top-7 z-[-1]">
              <svg
                width="77"
                height="172"
                viewBox="0 0 77 172"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle cx="86" cy="86" r="86" fill="url(#paint0_linear)" />
                <defs>
                  <linearGradient
                    id="paint0_linear"
                    x1="86"
                    y1="0"
                    x2="86"
                    y2="172"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#0D9488" stop-opacity="0.09" />
                    <stop offset="1" stop-color="#C4C4C4" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </span>
            <span class="absolute right-4 top-4 z-[-1]">
              <svg
                width="41"
                height="89"
                viewBox="0 0 41 89"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle
                  cx="38.9138"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 38.9138 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 38.9138 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 38.9138 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 38.9138 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 38.9138 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 38.9138 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 38.9138 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="1.42021"
                  r="1.42021"
                  transform="rotate(180 38.9138 1.42021)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 26.4157 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 26.4157 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 26.4157 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 26.4157 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 26.4157 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 26.4157 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 26.4157 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 26.4157 1.4202)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 13.9177 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 13.9177 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 13.9177 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 13.9177 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 13.9177 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 13.9177 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 13.9177 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="1.42019"
                  r="1.42021"
                  transform="rotate(180 13.9177 1.42019)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 1.41963 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 1.41963 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 1.41963 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 1.41963 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 1.41963 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 1.41963 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 1.41963 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 1.41963 1.4202)"
                  fill="#0D9488"
                />
              </svg>
            </span>
          </div>
        </div>
      </div>
      <div class="w-full px-4 md:w-1/2 lg:w-1/3">
        <div
          class="border-primary-600 shadow-lg relative z-10 mb-10 overflow-hidden rounded-xl border border-opacity-20 bg-white py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12"
        >
          <span class="text-primary-600 mb-4 block text-lg font-semibold">
            Professional
          </span>
          <h2 class="text-gray-900 mb-5 text-[42px] font-bold">
            GHS 30
            <span class="text-gray-600 text-base font-medium"> / subject </span>
          </h2>
          <p
            class="text-gray-600 mb-8 border-b border-[#F2F2F2] pb-8 text-base"
          >
            Perfect for pragmatic learners and anyone needing more time.
          </p>
          <div class="mb-7">
            <p class="text-gray-600 mb-1 text-base leading-loose">
              All features
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              Free updates
            </p>
            <p class="text-gray-600 mb-1 text-base leading-loose">
              12 Months access
            </p>
          </div>
          <a
            href="{{ route('sign-in') }}"
            class="text-primary-600 hover:bg-primary-600 hover:border-primary-600 block w-full rounded-md border border-[#D4DEFF] bg-transparent p-4 text-center text-base font-semibold transition hover:text-white"
          >
            Choose Professional
          </a>
          <div>
            <span class="absolute right-0 top-7 z-[-1]">
              <svg
                width="77"
                height="172"
                viewBox="0 0 77 172"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle cx="86" cy="86" r="86" fill="url(#paint0_linear)" />
                <defs>
                  <linearGradient
                    id="paint0_linear"
                    x1="86"
                    y1="0"
                    x2="86"
                    y2="172"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#0D9488" stop-opacity="0.09" />
                    <stop offset="1" stop-color="#C4C4C4" stop-opacity="0" />
                  </linearGradient>
                </defs>
              </svg>
            </span>
            <span class="absolute right-4 top-4 z-[-1]">
              <svg
                width="41"
                height="89"
                viewBox="0 0 41 89"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle
                  cx="38.9138"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 38.9138 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 38.9138 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 38.9138 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 38.9138 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 38.9138 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 38.9138 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 38.9138 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="38.9138"
                  cy="1.42021"
                  r="1.42021"
                  transform="rotate(180 38.9138 1.42021)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 26.4157 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 26.4157 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 26.4157 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 26.4157 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 26.4157 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 26.4157 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 26.4157 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="26.4157"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 26.4157 1.4202)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 13.9177 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 13.9177 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 13.9177 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 13.9177 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 13.9177 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 13.9177 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 13.9177 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="13.9177"
                  cy="1.42019"
                  r="1.42021"
                  transform="rotate(180 13.9177 1.42019)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="87.4849"
                  r="1.42021"
                  transform="rotate(180 1.41963 87.4849)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="74.9871"
                  r="1.42021"
                  transform="rotate(180 1.41963 74.9871)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="62.4892"
                  r="1.42021"
                  transform="rotate(180 1.41963 62.4892)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="38.3457"
                  r="1.42021"
                  transform="rotate(180 1.41963 38.3457)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="13.634"
                  r="1.42021"
                  transform="rotate(180 1.41963 13.634)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="50.2754"
                  r="1.42021"
                  transform="rotate(180 1.41963 50.2754)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="26.1319"
                  r="1.42021"
                  transform="rotate(180 1.41963 26.1319)"
                  fill="#0D9488"
                />
                <circle
                  cx="1.41963"
                  cy="1.4202"
                  r="1.42021"
                  transform="rotate(180 1.41963 1.4202)"
                  fill="#0D9488"
                />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>





<section id="faq" class="py-10 bg-white sm:py-16 lg:py-24">
    <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold leading-tight text-black sm:text-4xl lg:text-5xl">Frequently Asked Questions</h2>

        <div class="flow-root mt-12 sm:mt-16">
            <div class="divide-y divide-gray--200 -my-9">
                <div class="py-9">
                    <p class="text-xl font-semibold text-black">How to Create an Account?</p>
                    <p class="mt-3 text-base text-gray-600">Please, click on the “No account yet? Sign Up” option below the Login provision on this page. Provide your full name under “Name”, your email address under “Email”, a password of your choice under “Password” and repeat the password under “Confirm Password”. Click on the “Sign UP” button (green bar) after you have filled out all the information required for signing up.</p>
                </div>

                <div class="py-9">
                    <p class="text-xl font-semibold text-black">How to Subscribe?</p>
                    <p class="mt-3 text-base text-gray-600">At the top right corner of your home page is your initials, which is a dropdown. Click on it to display the items. Choose “Subscriptions” from the menu. Now click on “New Subscription” on the blank subscription page. Under “package” are two options: individual subscription and institutional subscription. Choose the option you want to subscribe for. Under “duration” are three options: 3 months subscription, 6 months subscription and 12 months subscription. Choose the duration you want. Next, click on the category of school that pertains to you (e.g., Primary/Basic School) and choose the level that applies to you (e.g., Basic Three). This will list all the subjects captured under the level. Now, click on the boxes (on the left hand of the subjects) of the subjects you want to subscribe for. Under “Amount” at the bottom of the page shows, the subscription fee you need to pay for your selection for the duration of your subscription. Inspect and click “Create Subscription” (green bar to the right) to submit your subscription to the LAAPP server.</p>
                </div>

                <div class="py-9">
                    <p class="text-xl font-semibold text-black">How to Pay for Your Subscription?</p>
                    <p class="mt-3 text-base text-gray-600">Payment is made using phone. Please, dial *772*30#, then enter 1326001 as the merchant code and send. Next, choose option 1 if you want to pay using mobile money and option 2 if you want to pay with a Prudential Bank account. A request page for your subscription reference number will appear next. Please, inter exactly the reference number that was generated by the LAAPP system for your subscription and send. In the final stage, you choose 1 to confirm your payment and submit to the bank.</p>
                    <p class="mt-3 text-base text-gray-600">After the payment, your subscription status will change from “unpaid” to “paid” and you are now ready use the system.</p>
                </div>
            </div>
        </div>
    </div>
</section>




<footer class="bg-white rounded-lg shadow">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <a href="https://flowbite.com/" class="flex items-center mb-4 sm:mb-0">
                <img src="{{ asset('img/logo.png') }}" class="h-8 mr-3" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap">{{ config('app.name') }}</span>
            </a>
            <ul class="flex flex-wrap items-center mb-6 text-sm font-medium text-gray-500 sm:mb-0">
                <li>
                    <a href="#home" class="mr-4 hover:underline md:mr-6 ">Home</a>
                </li>
                <li>
                    <a href="#about" class="mr-4 hover:underline md:mr-6 ">About</a>
                </li>
                <li>
                    <a href="#features" class="mr-4 hover:underline md:mr-6">Features</a>
                </li>
                <li>
                    <a href="#pricing" class="mr-4 hover:underline md:mr-6 ">Pricing</a>
                </li>
                <li>
                    <a href="#faq" class="hover:underline">FAQ</a>
                </li>
            </ul>
        </div>
        <hr class="my-6 border-gray-200 sm:mx-auto lg:my-8" />
        <span class="block text-sm text-gray-500 sm:text-center">© <script>document.write(new Date().getFullYear())</script> {{ config('app.name') }}™. All Rights Reserved.</span>
    </div>
</footer>


</x-app>