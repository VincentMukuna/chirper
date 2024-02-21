import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import Notification from "@/Components/Notification.jsx";
import {User, UserActions, UserAvatar, UserDetails} from "@/Components/User.jsx";

export default function Index({auth, users}){



    return(
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <div className="bg-white shadow-sm rounded-lg divide-y">
                    {users.map((user) => (
                        <User key={user.id} user={user}>
                            <UserAvatar />
                            <UserDetails />
                            <UserActions />
                        </User>
                    ))}
                </div>
            </div>
        </AuthenticatedLayout>

    )

}
