<div>
    <x-mary-card title="Edit Group Details">
        <form wire:submit.prevent="saveDetails">
            <div class="space-y-6">
                <x-mary-input 
                    label="Group Name" 
                    wire:model="name" 
                    placeholder="Enter group name" 
                    required 
                />

                <x-mary-textarea 
                    label="Description" 
                    wire:model="description" 
                    placeholder="Enter group description (optional)"
                    rows="3" 
                />

                <x-mary-checkbox 
                    label="Active Group" 
                    wire:model="is_active" 
                    hint="Only active groups can have new members"
                />

                <div class="flex justify-end space-x-3">
                    <x-mary-button type="submit" class="btn-primary" spinner="saveDetails">
                        Save Changes
                    </x-mary-button>
                </div>
            </div>
        </form>
    </x-mary-card>
</div>
