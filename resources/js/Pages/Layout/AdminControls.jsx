import { Link, usePage } from "@inertiajs/react";
import Button from '../Components/Button'

export default function AdminControls(){
    const { auth } = usePage().props;

    if(auth.is_admin){
        return(
            <Button as={Link}
                    color='gray'
                    href="/showtimes/create"
             >
                Create Showtime
            </Button>
        )
    }
}