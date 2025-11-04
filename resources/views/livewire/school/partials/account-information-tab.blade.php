<section>
    <div x-show="activeTab === 'account-information'" class="animate-fade-in">
        <livewire:common.payment-account-manager :model="$school" model-type="school" />
    </div>
</section>

