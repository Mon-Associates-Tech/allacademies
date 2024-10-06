<x-auth title="Edit Profile">
    

    <form method="POST" action="{{ route('profile.update') }}" class="bg-white p-4 rounded-lg w-[70%] mx-auto">
	@csrf
        @method('PATCH')
	<div class="sm:grid sm:grid-cols-[38%_60%] gap-4 flex flex-col items-center">
		<div class="p-4 bg-blue-500 border rounded-2xl h-28 w-28 sm:h-full sm:w-full">
			<img>
		</div>
		<div class="flex flex-col gap-4">
			<div>
                		<x-form.file name="avatar" />
            		</div>
            		<div>
                		<x-form.checkbox name="force_update_avatar" label="Force Update Avatar" description="If checked, previous avatar is deleted even without selecting a new one"  />
            		</div>
            		<div >
                		<x-form.input name="name" type="text" :value="$user->name" />
            		</div>
            		<div>
                		<x-form.input name="email" type="email" :value="$user->email" />
            		</div>
			
		</div>

	</div>
	<div class="flex justify-center mt-3">
            <x-button.primary class="ml-2">Update Profile</x-button.primary>
        </div>
</form>
</x-auth>