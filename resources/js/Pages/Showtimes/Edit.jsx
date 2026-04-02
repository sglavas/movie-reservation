import Layout from "../Components/Layout"
import ShowtimeInformation from "./Components/ShowtimeInformation"
import ShowtimeAttributes from "./Components/ShowtimeAttributes"
import { usePage } from "@inertiajs/react"
import { useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react'
import Button from "../Components/Button"
import ShowtimeDetails from "./Components/ShowtimeDetails"

export default function Edit() {
    const { showtime } = usePage().props;

    // Implement Inertia useForm hook
    // Fill the form inputs using showtime information
    const { patch, data, setData, reset, processing } = useForm({
        // Use movie, theater and screen id for data integrity
        movie: showtime['movie_id'],
        theater: showtime['theater_id'],
        screen: showtime['screen_id'],
        date: showtime['date'],
        time: showtime['start_time'],
        subtitles: showtime['subtitles'],
        is_3d: showtime['is_3d'],
        dubbed: showtime['dubbed'],
    })

    const submit = (e) => {
        e.preventDefault()
        patch(`/showtimes/${showtime.id}`);
    }

    return(
        <div>
            <div className="border-b border-white/10 pb-12">
              <ShowtimeDetails />
            </div>
            <form onSubmit={submit} action="/showtimes">
                <div className="space-y-12">
                    <ShowtimeInformation data={data} 
                                         setData={setData}
                    />

                    <ShowtimeAttributes data={data}
                                        setData={setData} 
                    />
                </div>

                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <Button as={Link}
                            color='red'
                            type='button'
                            href={`/showtimes/${showtime.id}`}
                    >
                        Cancel
                    </Button>
                    <Button as='button'
                            color='indigo'
                            type='submit'
                            disabled={processing}
                    >
                        Update
                    </Button>
                </div>
            </form>
        </div>
    )
}

Edit.layout = page => <Layout children={page} slot="Edit Showtime" />