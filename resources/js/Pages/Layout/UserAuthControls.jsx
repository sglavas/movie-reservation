import { useForm, usePage } from "@inertiajs/react";
import NavigationLink from "./NavigationLink";
import Button from "../Components/Button";

export default function UserAuthControls() {
    const { auth } = usePage().props;
    const { post, processing } = useForm();

    const submit = (e) => {
        e.preventDefault();
        post('/logout');
    };

    if(!auth.user){
        return(
            <NavigationLink 
                navigation={[ {name: 'Log In', href: '/login'}, {name: 'Register', href: '/register'} ]}
            />
        );
    }else{
        return(
            <form onSubmit={submit} action="/logout">
                <Button as='button'
                        color='indigo'
                        type='submit'
                        disabled={processing}
                >
                    Log Out
                </Button>
            </form>
        )
    }
}