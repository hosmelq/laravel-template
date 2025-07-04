interface HomeProps {
  now: string
}

export default function Home({now}: HomeProps) {
  return (
    <div className="flex min-h-screen items-center justify-center">
      <h1 className="text-3xl font-bold underline">Home - {now}</h1>
    </div>
  )
}
