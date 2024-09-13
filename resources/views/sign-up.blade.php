<x-app>

<section class="bg-white">
  <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
    <aside class="relative block h-16 lg:order-last lg:col-span-5 lg:h-full xl:col-span-6">
      <img
        alt=""
        src="https://images.unsplash.com/photo-1605106702734-205df224ecce?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80"
        class="absolute inset-0 h-full w-full object-cover"
      />
    </aside>

    <main
      class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-7 lg:px-16 lg:py-12 xl:col-span-6"
    >
      <div class="max-w-xl lg:max-w-3xl">
        <x-logo>Login</x-logo>

        <h1 class="mt-6 text-2xl font-bold text-gray-900 sm:text-3xl md:text-4xl">
          Welcome to All Academies
        </h1>

        <p class="mt-4 leading-relaxed text-gray-500">
          Fill in the form below to create an account
        </p>

        <form method="POST" action="{{ route('sign-up') }}" class="space-y-3">
        @csrf
        <x-form.input name="name" type="text" />
        <x-form.input name="email" type="email" />
        <x-form.password name="password" />
        <x-form.password name="password_confirmation" label="Confirm Password" />
        <x-button.primary class="w-full justify-center">Sign Up</x-button.primary>
        </form>
        <a href="{{ route('sign-in') }}" class="block w-full mt-5 text-center text-primary-600 text-sm">Already have an account? Sign In</a>
      </div>
    </main>
  </div>
</section>

</x-app>